<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
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
        // dd($product->getAttributes());
        if(!$product->on_sale){
            throw new InvalidRequestException('商品未上架');
        }
        if($user = $request->user()){
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        return view('products.show',[
            'product'   =>  $product,
            'favored'   =>  $favored
        ]);
    }

    public function favor(Product $product,Request $request)
    {
        $user = $request->user();

        if($user->favoriteProducts()->find($product->id)){
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product,Request $request)
    {
        $user = $request->user();
        
        $user->favoriteProducts()->detach($product->id);
        return [];
    }
}
