<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $builder = Product::query()->where('on_sale',true);
        //模糊查询
        if($search = $request->input('search','')){

            $like = '%'.$search.'%';

            $builder->where(function($query) use ($like){
                $query->where('title','like',$like)
                    ->orWhere('description','like',$like)
                    ->orWhereHas('skus',function($query) use ($like){
                        $query->where('title','like',$like)
                            ->orWhere('description','like',$like);
                    });
            });
        }

        //排序
        if($order = $request->input('order','')){
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['price','rating','sold_count'])){
                    $builder->orderBy($m[1],$m[2]);
                }
            }
        }

        $products = $builder->paginate(16);

        return view('products.index',[
            'products'  =>  $products,
            'filters'   =>  [
                'search'    =>  $search,
                'order' =>  $order
            ]
        ]);
    }

    public function show(Product $product,Request $request)
    {
        if(!$product->where('on_sale',true)){
            throw new \Exception('商品未上架');
        }

        return view('products.show',['product' =>  $product]);
    }
}
