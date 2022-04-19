<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{   
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        $address = $request->user()->addresses()->orderBy('last_used_at','desc')->get();
        return view('cart.index',[
            'cartItems' =>  $cartItems,
            'addresses'   =>  $address
        ]);
    }

    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'),$request->input('amount'));
        return [];
    }

    public function remove(ProductSku $sku,Request $request)
    {
        $this->cartService->remove($sku->id);
        return [];
    }
}
