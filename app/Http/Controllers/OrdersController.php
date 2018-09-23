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
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewd;
use App\Http\Requests\ApplyRefundRequest;


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

    public function review(Order $order){
        $this->authorize('own',$order);
        //判断是否已经支付
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //使用load方法加载关联数据 避免N+1性能问题
        return view('orders.review',['order' => $order->load(['items.productSku','items.product'])]);
    }

    public function sendReview(Order $order,SendReviewRequest $request){
        $this->authorize('own',$order);
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //判断是否已经评价
        if($order->reviewed){
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        //开启事务
        \DB::transaction(function() use ($reviews,$order){
            //遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                //保存评分和评价
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            $order->update(['reviewed' => true]);
            event(new OrderReviewd($order));
        });

        return redirect()->back();
    }

    public function applyRefund(Order $order,ApplyRefundRequest $request){
        $this->authorize('own',$order);

        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        //判断订单退款状态是否正确
        if($order->refund_status !== Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('该订单已申请过退款，请勿重复申请');
        }
        //将用户输入的退款理由放到订单的extra字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');
        //将订单退款状态改为已退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;
    }
}
