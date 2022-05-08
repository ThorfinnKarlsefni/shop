<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product','items.productSku'])
            ->where('user_id',$request->user()->id)
            ->orderBy('created_at','desc')
            ->paginate();
        
        return view('orders.index',[
            'orders'    =>  $orders
        ]);
    }

    public function show(Order $order)
    {
        $this->authorize('own',$order);
        return view('orders.show',[
            'order' => $order->load(['items.product','items.productSku'])
        ]);
    }

    public function store(OrderRequest $request,OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        
        return $orderService->store($user,$address,$request->input('remark'),$request->input('items'));
    }

    public function received(Order $order,Request $request)
    {
        $this->authorize('own',$order);

        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不明确');
        }

        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return redirect()->back();
    }

    public function review(Order $order)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付,不可评价');
        }

        return view('orders.review',['order' => $order->load(['items.productSku','items.product'])]);
    }

    public function sendReview(Order $order,SendReviewRequest $request)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            return new InvalidRequestException('该订单未支付,不可评价');
        }

        if($order->reviewed){
            return new InvalidRequestException('该订单已评价');
        }

        $reviews = $request->input('reviews');
        // dd($reviews);
        \DB::transaction(function() use($reviews,$order){
            foreach($reviews as $reviews){

                $orderItem = $order->items()->find($reviews['id']);

                $orderItem->update([
                    'rating'    =>  $reviews['rating'],
                    'review'   =>  $reviews['review'],
                    'reviewed_at'   =>  Carbon::now(),
                ]);
            }
            $order->update(['reviewed' => true]);
        });

        event(new OrderReviewed($order));
        return redirect()->back();
    }

    public function applyRefund(Order $order,ApplyRefundRequest $request)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
             throw new InvalidRequestException('该订单未付款，不可退款');
        }

        if($order->refund_status !== $order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('该订单已申请过退款,请勿重复申请');
        }

        $extra  = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');

        $order->update([
            'refund_status' =>  Order::REFUND_STATUS_APPLIED,
            'extra' =>  $extra,
        ]);

        return $order;
    }
}
