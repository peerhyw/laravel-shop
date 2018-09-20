<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //判断用户提交的地址id是否存在于数据库并且属于当前用户
            //后面这个条件非常重要，否则恶意用户可以用不同的地址id不断提交订单来遍历出平台所有收货地址
            'address_id' => ['required',Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)],
            'items' => ['required','array'],
            'items.*.sku_id' => [ //检查items数组下每一个子数组的sku_id参数
                'required',
                /*在检查库存时，我们需要获取用户想要购买的该 SKU 数量，我们可以通过匿名函数的第一个参数 $attribute 来获取当前 SKU 所在的数组索引，比如第一个 SKU 的 $attribute 就是 items.0.sku_id，所以我们采用正则的方式将这个 0 提取出来，$this->input('items')[0]['amount'] 就是用户想购买的数量。*/
                function ($attribute,$value,$fail){
                    if(!$sku = ProductSku::find($value)){
                        $fail('该商品不存在');
                        return;
                    }
                    if(!$sku->product->on_sale){
                        $fail('该商品未上架');
                        return;
                    }
                    if($sku->stock === 0){
                        $fail('该商品已售完');
                        return;
                    }
                    //获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/', $attribute,$m);
                    $index = $m[1];
                    //根据索引找到用户所提交的购买数量
                    $amount = $this->input('items')[$index]['amount'];
                    if($amount > 0 && $amount > $sku->stock){
                        $fail('该商品库存不足');
                        return;
                    }
                },
            ],
            'items.*.amount' => ['required','integer','min:1'],
        ];
    }
}
