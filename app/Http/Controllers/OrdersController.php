<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrdersController extends Controller
{
    public function store(OrderRequest $request,OrderService $orderService){
    	$user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
    	
    	//cart/index.blade.php 的ajax返回的就是下面这个return
        return $orderService->store($user,$address,$request->input('remark'),$request->input('items'));
    }

    public function index(Request $request){
    	$orders = Order::query()
    				//使用with方法预加载
    				->with(['items.product','items.productSku'])
    				->where('user_id',$request->user()->id)
    				->orderBy('created_at','desc')
    				->paginate();
    	return view('orders.index',['orders' => $orders]);
    }

    public function show(Order $order,Request $request){
        $this->authorize('own',$order);
    	//这里的 load() 方法与上一章节介绍的 with() 预加载方法有些类似，称为 延迟预加载，不同点在于 load() 是在已经查询出来的模型上调用，而 with() 则是在 ORM 查询构造器上调用。
    	return view('orders.show',['order' => $order->load(['items.productSku','items.product'])]);
    }

    public function received(Order $order,Request $request){
        $this->authorize('own',$order);

        //判断订单的发货状态是否为已发货
        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不正确');
        }

        //更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        //返回订单信息
        return $order;
    }
}
