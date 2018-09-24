<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;

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

    	$this->afterPaid($order);
    	return app('alipay')->success();
    	/*$data = app('alipay')->verify();
        \Log::debug('Alipay notify', $data->all());*/
    }

    public function payByWechat(Order $order,Request $request){
    	//校验权限
    	$this->authorize('own',$order);
    	//校验订单状态
    	if($order->paid_at || $order->closed){
    		throw new InvalidRequestException("订单状态不正确");
    	}

    	//scan方法为拉起微信扫码支付
    	$wechatOrder = app('wechat_pay')->scan([
    		'out_trade_no' => $order->no,
    		'total_fee' => $order->total_amount * 100,//与支付宝不同，微信支付的金额单位是分
    		'body' => '支付 Laravel Shop 的订单：'.$order->no,//订单描述
    	]);
    	//把要转换的字符串作为QrCode的构造函数参数
    	$qrCode = new QrCode($wechatOrder->code_url);
    	//将生成的二维码图片数据以字符串形式输出，并带上相应的响应类型
    	return response($qrCode->writeString(),200,['Content-Type' => $qrCode->getContentType()]);
    }

    public function wechatNotify(){
    	//校验回调参数是否正确
    	$data = app('wechat_pay')->verify();
    	//找到对应的订单
    	$order = Order::where('no',$data->out_trade_no)->first();
    	//订单不存在则告知微信支付
    	if(!$order){
    		return 'fail';
    	}
    	//订单已支付
    	if($order->paid_at){
    		//告知微信支付此订单已处理
    		return app('wechat_pay')->success();
    	}
    	//将订单标记为已支付
    	$order->update([
    		'paid_at' => Carbon::now(),
    		'payment_method' => 'wechat',
    		'payment_no' => $data->transaction_id,
    	]);

    	$this->afterPaid($order);
    	return app('wechat_pay')->success();
    }

    public function afterPaid(Order $order){
    	event(new OrderPaid($order));
    }
}
