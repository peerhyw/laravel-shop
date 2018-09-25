<?php

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Faker\Generator::class);
        // 创建 100 笔订单
        $orders = factory(Order::class,100)->create();
        //被购买的商品，用于后面更新商品销量和评分
        $products = collect([]);
        foreach($orders as $order){
        	//每笔订单随机1-3个商品
        	$items = factory(OrderItem::class,random_int(1,3))->create([
        		'order_id' => $order->id,
        		'rating' => $order->reviewed ? random_int(1,5) : null, //随机评分1-5
        		'review' => $order->reviewed ? $faker->sentence : null,
        		'reviewed_at' => $order->reviewed ? $faker->dateTimeBetween($order->paid_at) : null,
        	]);

        	//计算总价
        	$total = $items->sum(function(OrderItem $item){
        		return $item->price * $item->amount;
        	});

        	//如果有优惠券，则计算优惠后的价格
        	if($order->couponCode){
        		$total = $order->couponCode->getAdjustedPrice($total);
        	}

        	//更新总价
        	$order->update([
        		'total_amount' => $total,
        	]);

        	//将这笔订单的商品合并到商品集合中
        	$products = $product->merge($items->pluck('product'));
        }

        //根据商品id过滤掉重复的商品
        $products->unique('id')->each(function(Product $product){
        	//查出该商品的销量，评分，评价数
        	$result = OrderItem::query()
        		->where('product_id',$product->id)
        		->whereHas('order',function($query){
        			$query->whereNotNull('paid_at');
        		})->first([
        			\DB::raw('count(*) as review_count'),
        			\DB::raw('avg(rating) as rating'),
        			\DB::raw('sum(amount) as sold_count'),
        		]);
        	$product->update([
        		'rating' => $result->rating ?: 5, //没评分 默认5分
        		'review_count' => $result->review_count,
        		'sold_count' => $result->sold_count,
        	]);
        });
    }
}
