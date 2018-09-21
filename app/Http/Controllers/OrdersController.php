<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;

class OrdersController extends Controller
{
    public function store(OrderRequest $request){
    	$user = $request->user();
    	//开启一个数据库事务
    	/*DB::transaction() 方法会开启一个数据库事务，在回调函数里的所有 SQL 写操作都会被包含在这个事务里，如果回调函数抛出异常则会自动回滚这个事务，否则提交事务。用这个方法可以帮我们节省不少代码。*/
    	$order = \DB::transaction(function() use ($user,$request){
    		$address = UserAddress::find($request->input('address_id'));
    		//更新此地址的最后使用时间
    		$address->update(['last_used_at' => Carbon::now()]);
    		//创建一个订单
    		$order = new Order([
    			'address' => [ //将地址信息放入订单中
    				'address' => $address->full_address,
    				'zip' => $address->zip,
    				'contact_name' => $address->contact_name,
    				'contact_phone' => $address->contact_phone,
    			],
    			'remark' => $request->input('remark'),
    			'total_amount' => 0,
    		]);
    		//订单关联到当前用户
    		$order->user()->associate($user);
    		//写入数据库
    		$order->save();

    		$totalAmount = 0;
    		$items = $request->input('items');
    		//遍历用户提交的sku
    		foreach ($items as $data) {
    			$sku = ProductSku::find($data['sku_id']);
    			//创建一个OrderItem并直接与当前订单关联
    			/*$order->items()->make() 方法可以新建一个关联关系的对象（也就是 OrderItem）但不保存到数据库，这个方法等同于 $item = new OrderItem(); $item->order()->associate($order);。*/
    			$item = $order->items()->make([
    				'amount' => $data['amount'],
    				'price' => $sku->price,
    			]);
    			$item->product()->associate($sku->product_id);
    			$item->productSku()->associate($sku);
    			$item->save();
    			$totalAmount += $sku->price * $data['amount'];
    			/*如果减库存失败则抛出异常，由于这块代码是在 DB::transaction() 中执行的，因此抛出异常时会触发事务的回滚，之前创建的 orders 和 order_items 记录都会被撤销。*/
    			if($sku->decreaseStock($data['amount']) <= 0){
    				throw new InvalidRequestException('该商品库存不足');
    			}
    		}

    		//更新订单总金额
    		$order->update(['total_amount' => $totalAmount]);

    		//将下单的商品从购物车中移除
    		$skuIds = collect($request->input('items'))->pluck('sku_id');
    		$user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();

    		return $order;
    	});
    	$this->dispatch(new CloseOrder($order,config('app.order_ttl')));

    	return $order;
    }
}
