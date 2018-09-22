<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderItem;

class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        //从事件对象中取出对应的订单
        $order = $event->getOrder();
        //循环遍历订单的商品
        foreach ($order->items as $item) {
            //从事件对象中取出对应的订单
            $product = $item->product;
            //计算对应的商品的销量
            $soldCount = OrderItem::query()
                        ->where('product_id',$product->id)
                        ->whereHas('order',function($query){
                            $query->whereNotNull('paid_at');//关联的订单状态是已支付
                        })->sum('amount');
            //更新商品销量
            $product->update([
                'sold_count' => $soldCount,
            ]);
        }
    }
}
