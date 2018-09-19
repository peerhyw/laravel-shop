@extends('layouts.app')
@section('title',$product->title)

@section('content')
<div class="row">
	<div class="col-lg-10 col-lg-offset-1">
		<div class="panel panel-default">
			<div class="panel-body product-info">
				<div class="row">
					<div class="col-sm-5">
						<img src="{{ $product->image_url }}" alt="" class="cover">
					</div>
					<div class="col-sm-7">
						<div class="title">{{ $product->title }}</div>
						<div class="price">
							<label>价格</label><em>￥</em><span>{{ $product->price }}</span>
						</div>
						<div class="sales_and_reviews">
							<div class="sold_count">
								累计销量 <span class="cout">{{ $product->sold_count }}</span>
							</div>
							<div class="review_count">
								累计销量 <span class="cout">{{ $product->review_count }}</span>
							</div>
							<div class="rating" title="评分 {{ $product->rating }}">
								评分 <span class="count">
									{{ str_repeat('★',floor($product->rating)) }}
									{{ str_repeat('☆',5-floor($product->rating)) }}
								</span>
							</div>
						</div>
						<div class="skus">
							<label>选择</label>
							<!-- <div class="btn-group" data-toggle="buttons"> ... </div> 这里使用了 Bootstrap 的按钮组来输出 SKU 列表。 -->
							<div class="btn-group" data-toggle="buttons">
								@foreach($product->skus as $sku)
									<label 
										class="btn btn-default sku-btn" 
										data-price="{{ $sku->price }}"
										data-stock="{{ $sku->stock }}"
										data-toggle="tooltip"
										title="{{ $sku->description }}"
										data-placement="bottom">
										<input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}">
										{{ $sku->title }}
									</label>
								@endforeach
							</div>
						</div>
						<div class="cart_amount">
							<label>数量</label>
							<input type="text" class="form-control input-sm" value="1">
							<span>件</span>
							<span class="stock">库存：<div class="stock_amount"><b></b></div>件</span>
						</div>
						<div class="buttons">
							<button class="btn btn-success btn-favor">❤ 收藏</button>
							<button class="btn btn-primary btn-add-to-cart">加入购物车</button>
						</div>
					</div>
				</div>
				<div class="product-detail">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active" role="presentation">
							<a href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab">
								商品详情
							</a>
						</li>
						<li role="presentation">
							<a href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tba" data-toggle="tab">
								用户评价
							</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" role="tabpanel" id="product-detail-tab">
							<!-- 因为我们后台编辑商品详情用的是富文本编辑器，提交的内容是 Html 代码，此处需要原样输出而不需要进行 Html 转义。 -->
							{{!! $product->description !!}}
						</div>
						<div class="tab-pane" role="tabpanel" id="product-reviews-tab">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
		$('.sku-btn').click(function(){
			$('.product-info .price span').text($(this).data('price'));
			$('.product-info .stock_amount b').text($(this).data('stock'));
		});
	});
</script>
@endsection