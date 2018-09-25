<?php

use Faker\Generator as Faker;
use App\Models\Product;
use App\Models\OrderItem;

$factory->define(App\Models\OrderItem::class, function (Faker $faker) {
	//从数据库随机取一条商品
	$product = Product::query()->where('on_sale',true)->inRandomOrder()->first();
	//从该商品的sku中随机取一条
	$sku = $product->skus()->inRandomOrder()->first();
    return [
        'amount' => random_int(1,5),//购买数量随机1-5份
        'price' => $sku->price,
        'rating' => null,
        'review' => null,
        'reviewed_at' => null,
        'product_id' => $product->id,
        'product_sku_id' => $sku->id,
    ];
});
