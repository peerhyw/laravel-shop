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
								累计评论 <span class="cout">{{ $product->review_count }}</span>
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
							@if($favored)
								<button class="btn btn-success btn-disfavor">取消收藏</button>
							@else
								<button class="btn btn-success btn-favor">❤ 收藏</button>
							@endif
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
							<!-- 评论列表开始 -->
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<td>用户</td>
										<td>商品</td>
										<td>评分</td>
										<td>评价</td>
										<td>时间</td>
									</tr>
								</thead>
								<tbody>
									@foreach($reviews as $review)
										<tr>
											<td>{{ $review->order->user->name }}</td>
											<td>{{ $review->productSku->title }}</td>
											<td>{{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5 - $review->rating) }}</td>
											<td>{{ $review->review }}</td>
											<td>{{ $review->reviewed_at->format('Y-m-d H:i') }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<!-- 评论列表结束 -->
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
		//监听收藏按钮的点击时间
		$('.btn-favor').click(function(){
			//发起一个post ajax请求，请求url通过后端的route()函数生成
			axios.post('{{ route('products.favor',['product' => $product->id]) }}')
				.then(function(){//请求成功会执行这个回调
					swal('操作成功','','success')
						.then(function(){
							location.reload();
						});
				},function(error){//请求失败会执行这个回调
					//如果返回码是401代表没登录
					if(error.response && error.response.status === 401){
						swal('请先登录','','error');
					}else if(error.response && error.response.data.msg){
						//其他有msg字段的情况，将msg提示给用户
						swal(error.response.data.msg,'','error');
					}else{
						//其他情况应该是系统挂了
						swal('系统错误','','error');
					}
				});
		});
		$('.btn-disfavor').click(function(){
			axios.delete('{{ route('products.disfavor',['product' => $product->id]) }}')
				.then(function(){
					swal('操作成功','','success')
						.then(function(){
							location.reload();
						});
				});
		});
		//加入购物车按钮点击事件
		$('.btn-add-to-cart').click(function(){
			//请求加入购物车接口
			axios.post('{{ route('cart.add') }}',{
				//当用户点击 加入购物车 按钮时，通过 $('label.active input[name=skus]') 这个 CSS 选择器取得当前被选中的 SKU，并取得对应的 ID。
				sku_id: $('label.active input[name=skus]').val(),
				amount: $('.cart_amount input').val(),
			}).then(function(){ //请求成功执行此回调
					swal('加入购物车成功','','success')
						.then(function(){
							location.href = '{{ route('cart.index') }}';
						});
				},function(error){//请求失败执行此回调
					if(error.response.status === 401){
						//http状态码为401代表用户未登录
						swal('请先登录','','error');
					}else if(error.response.status === 422){
						//http状态码为422代表用户输入校验失败
						var html = '<div>';
						_.each(error.response.data.errors,function(errors){
							_.each(errors,function(error){
								html += error+'<br>';
							})
						});
						html += '</div>';
						swal({content: $(html)[0],icon: 'error'})
					}else{
						//其他情况应该是系统挂了
						swal('系统错误','','error');
					}
				})
		});
	});
</script>
@endsection