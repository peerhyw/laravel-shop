<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    /*
        $this->app->singleton() 往服务容器中注入一个单例对象，第一次从容器中取对象时会调用回调函数来生成对应的对象并保存到容器中，之后再去取的时候直接将容器中的对象返回。

        app()->environment() 获取当前运行的环境，线上环境会返回 production。对于支付宝，如果项目运行环境不是线上环境，则启用开发模式，并且将日志级别设置为 DEBUG。由于微信支付没有开发模式，所以仅仅将日志级别设置为 DEBUG。
     */
    public function register()
    {
        //往服务容器注入一个名为alipay的单例对象
        $this->app->singleton('alipay',function(){
            $config = config('pay.alipay');
            $config['notify_url'] = route('payment.alipay.notify');//'http://requestbin.leo108.com/1hlrda61';//服务器端回调地址
            $config['return_url'] = route('payment.alipay.return');//前端回调地址
            //判断当前项目运行环境是否为线上环境
            if(app()->environment() !== 'production'){
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            //调用yansongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay',function(){
            $config = config('pay.wechat');
            $config['notify_url'] = route('payment.wechat.notify');//'http://requestbin.leo108.com/1hlrda61';
            if(app()->environment() !== 'production'){
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            //调用一个yansongba\Pay来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }
}
