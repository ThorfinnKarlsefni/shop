<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(AddCartRequest $request)
    {
        $user = $request->user();
        $amount = $request->input('amount');
        $skuId = $request->input('sku_id');

        if($cart = $user->cartItems()->where('product_sku_id',$skuId)->first()){
            $cart->update([
                'amount' => $cart->amount + $amount
            ]);
        }else{
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }
}
