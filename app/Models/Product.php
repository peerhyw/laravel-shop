<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
    	'title','description','image','on_sale','rating','sold_count','review_count','price',
    ];

    protected $casts = [
    	'on_sale' => 'boolean',
    ];

    public function skus(){
    	return $this->hasMany(ProductSku::class);
    }

    //get访问器
    public function getImageUrlAttribute(){
    	//如果image字段本身就已经是完整的url就直接返回
    	if(Str::startsWith($this->attributes['image'],['http://','https://'])){
    		return $this->attributes['image'];
    	}
    	//这里 \Storage::disk('public') 的参数 public 需要和我们在 config/admin.php 里面的 upload.disk 配置一致。
    	return \Storage::disk('public')->url($this->attributes['image']);
    }
}
