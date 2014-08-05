<?php
$cart             = json_decode($order->cart, true);
$billing_address  = json_decode($order->billing_address);
$shipping_address = json_decode($order->shipping_address);

$Cart = App::make('Cart');
$Cart->load($cart);
?>
<div class="row">
	<div class="col-xs-12">
		<p>
			<b>Order ID:</b>
		</p>
		<p>
			{{ $order->id }}
		</p>
		<p>
			<b>Order Time:</b>
		</p>
		<p>
			{{ $order->created_at }}
		</p>
		@if (count($billing_address))
			<p>
				<b>Billed To:</b>
			<p>
			@include('products::orders.address', array('address'=>$billing_address))
		@endif
		<p>
			<b>Shipping To:</b>
		</p>
		@include('products::orders.address', array('address'=>$shipping_address))
	</div>
</div>
<hr />
@foreach ($cart as $key=>$item)
	<?php $product = json_decode($item['product']); ?>
	<div class="row">
		<div class="col-sm-3">
			@if (isset($product->images) && count($product->images))
				<a href="{{ url('products/' . $product->slug) }}">
					<img src="{{ $product->images[0]->image }}" style="width:100%" />
				</a>
			@endif
		</div>
		<div class="col-sm-3">
			<h4>
				<a href="{{ url('products/' . $product->slug) }}">
					{{ $product->name }}
				</a>
			</h4>
			<hr />
			@foreach ($Cart->getOptions($key) as $group_name=>$option)
				<p>
					<b>{{ $group_name }}:</b>
				</p>
				<p>
					{{ $option->name }}
				</p>
			@endforeach
		</div>
		<div class="col-sm-3">
			<h4>Price</h4>
			<hr />
			<h5 style="text-decoration:line-through;font-style:italic;">${{ number_format($item['fake_price'], 2) }}</h5>
			<h3>${{ number_format($item['price'], 2) }}</h3>
		</div>
		<div class="col-sm-3">
			<h4>Quantity</h4>
			<hr />
			{{ $item['qty'] }}
		</div>
	</div>
	<hr />
@endforeach
<div class="text-right">
	<h3>Total: ${{ number_format($order->total, 2) }}</h3>
</div>