<?php

namespace App\Listeners;

use DB;
use App\Models\OrderItem;
use App\Events\OrderReviewd;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductRating implements ShouldQueue
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
     * @param  OrderReviewd  $event
     * @return void
     */
    public function handle(OrderReviewd $event)
    {
        $items = $event->getOrder()->items()->with(['product'])->get();
        foreach ($items as $item) {
            $result = OrderItem::query()
                        ->where('product_id',$item->product_id)
                        ->whereHas('order',function($query){
                            $query->whereNotNull('paid_at');
                        })->first([//first() 方法接受一个数组作为参数，代表此次 SQL 要查询出来的字段
                            DB::raw('count(*) as review_count'),
                            DB::raw('avg(rating) as rating')
                        ]);
            //更新商品的评分和评价数
            $item->product->update([
                'rating' => $result->rating,
                'review_count' => $result->review_count,
            ]);
        }
    }
}
