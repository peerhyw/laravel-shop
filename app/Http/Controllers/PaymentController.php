<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order,Request $request){
    	//判断订单是否属于当前用户
    	$this->authorize('own',$order);
    	//订单已支付或者已关闭
    	if($order->paid_at || $order->closed){
    		throw new InvalidRequestException('订单状态不正确');
    	}

    	//调用支付宝的网页支付
    	return app('alipay')->web([
    		'out_trade_no' => $order->no,//订单编号，需保证在商户端不重复
    		'total_amount' => $order->total_amount,//订单金额，单位元，支持小数点后两位
    		'subject' => '支付 Laravel Shop 的订单：'.$order->no,//订单标题
    	]);
    }

    //前端回调页面
    public function alipayReturn(){
    	try {
    		app('alipay')->verify();
    	} catch (\Exception $e) {
    		return view('pages.error',['msg' => '数据不正确']);
    	}

    	return view('pages.success',['msg' => '付款成功']);
    }

    //服务器回调
    public function alipayNotify(){
    	//校验输入参数
    	$data = app('alipay')->verify();
    	//$data->out_trade_on拿到订单流水号，并在数据库中查询
    	$order = Order::where('no',$data->out_trade_no)->first();
    	//正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性
    	if(!$order){
    		return 'fail';
    	}
    	//如果这笔订单的状态已经是已支付
    	if($order->paid_at){
    		/*其中 app('alipay')->success() 返回数据给支付宝，支付宝得到这个返回之后就认为我们已经处理好这笔订单，不会再发生这笔订单的回调了。如果我们返回其他数据给支付宝，支付宝就会每隔一段时间就发送一次服务器端回调，直到我们返回了正确的数据为准。*/
    		// 返回数据给支付宝
    		return app('alipay')->success();
    	}
    	$order->update([
    		'paid_at' => Carbon::now(),//支付时间
    		'payment_method' => 'alipay',//支付方式
    		'payment_no' => $data->trade_no,//支付宝订单号
    	]);

    	return app('alipay')->success();
    	//\Log::debug('Alipay notify',$data->all());
    }
}
