<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();
        $order->load('items.product');
        
        foreach($order->items as $item){
            $product = $item->product;
            
            $soldCount = OrderItem::query()
                ->where('prodcut_id',$product->id)
                ->whereHas('order',function($query){
                    $query->whereNotNull('paid_at');
                })->sum('amount');
                
            $product->update([
                'sold_count'    => $soldCount,
            ]);
        }
    }
}
