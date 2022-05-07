<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => [
                'required',
                Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)
            ],
            'items' =>  ['required','array'],
            'items.*.sku_id'    =>  [
                'required',
                function($attribute,$value,$fail){
                    if(!$sku = ProductSku::find($value)){
                        return $fail('商品不能存在');
                    }

                    if(!$sku->product->on_sale){
                        return $fail('商品已下架');
                    }

                    if($sku->stock === 0){
                        return $fail('商品库存不足');
                    }

                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];

                    $amount = $this->input('items')[$index]['amount'];

                    if($amount > 0 && $amount > $sku->stock){
                        return $fail('该商品库存不足');
                    }
                }
            ],
            'items.*.amount' => [
                'required','min:1','integer'
            ]
        ];
    }
}
