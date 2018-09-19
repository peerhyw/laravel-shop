<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index(Request $request){

    	//创建一个查询构造器
    	$builder = Product::query()->where('on_sale',true);
    	//判断是否有提交search参数 如果有就复制给$search变量
    	//search参数用来模糊搜索商品
    	if($search = $request->input('search','')){
    		$like = '%'.$search.'%';
    		//模糊搜索商品标题，商品详情，sku标题，sku描述
    		/*用 $builder->where() 传入一个匿名函数，然后才在这个匿名函数里面再去添加 like 搜索，这样做目的是在查询条件的两边加上 ()，也就是说最终执行的 SQL 语句类似 select * from products where on_sale = 1 and ( title like xxx or description like xxx )
    		不使用匿名函数,那么生成的 SQL 就会变成 select * from products where on_sale = 1 and title like xxx or description like xxx，这个 SQL 会把 on_sale = 0 但 description 包含搜索词的商品也搜索出来，这不符合我们的期望。*/
    		$builder->where(function ($query) use ($like){
    			$query->where('title','like',$like)
    				->orWhere('description','like',$like)
    				->orWhereHas('skus',function ($query) use ($like){
    					$query->where('title','like',$like)
    						->orWhere('description','like',$like);
    				});
    		});
    	}

    	//是否有提交order参数，如果有就赋值给$order变量
    	//order参数用来控制商品的排序规则
    	if($order = $request->input('order','')){
    		//是否是以_asc 或者 _desc结尾
    		if(preg_match('/^(.+)_(asc|desc)$/', $order,$m)){
    			//如果字符串的开头是这3个字符串之一，说明是一个合法的排序值
    			if(in_array($m[1],['price','sold_count','rating'])){
    				//根据传入的排序值来构造排序函数
    				//$m[1] price sold_count rating $m[2] desc asc
    				$builder->orderBy($m[1],$m[2]);
    			}
    		}
    	}
    	$products = $builder->paginate(16);

    	return view('products.index',[
    		'products' => $products,
    		'filters' => [
    			'search' => $search,
    			'order' => $order,
    		],
    	]);
    }

    public function show(Product $product,Request $request){
    	//判断商品是否已经上架，如果没有上架则抛出异常
    	if(!$product->on_sale){
    		throw new InvalidRequestException("商品未上架");
    	}

    	$favored = false;
    	//用户未登录时返回的是null，已登录时返回的是对应的用户对象、
    	if($user = $request->user()){
    		//从当前用户已收藏的商品中搜索id为当前商品id的商品
    		//boolval()函数用于把值转为布尔值
    		$favored = boolval($user->favoriteProducts()->find($product->id));
    	}

    	return view('products.show',['product' => $product,'favored' => $favored]);
    }

    public function favor(Product $product,Request $request){
    	$user = $request->user();
    	if($user->favoriteProducts()->find($product->id)){
    		return [];
    	}
    	$user->favoriteProducts()->attach($product);
    	return [];
    }

    public function disfavor(Product $product,Request $request){
    	$user = $request->user();
    	$user->favoriteProducts()->detach($product);
    	return [];
    }
}
