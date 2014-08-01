<?php
$cart             = json_decode($order->cart);
$billing_address  = json_decode($order->billing_address);
$shipping_address = json_decode($order->shipping_address);
?>
<div class="row">
	<div class="col-xs-12">
		<p>
			<b>Order ID:</b>
		</p>
		<p>
			{{ $order->id }}
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
	<?php $product = json_decode($item->product); ?>
	<div class="row">
		<div class="col-sm-3">
			<a href="{{ url('products/' . $product->slug) }}">
				<img src="{{ $product->images[0]->image }}" style="width:100%" />
			</a>
		</div>
		<div class="col-sm-3">
			<h4>
				<a href="{{ url('products/' . $product->slug) }}">
					{{ $product->name }}
				</a>
			</h4>
			<hr />
			@foreach ($product->selected_options as $option)
				<p>{{ $option->name }}</p>
			@endforeach
		</div>
		<div class="col-sm-3">
			<h4>Price</h4>
			<hr />
			<h5 style="text-decoration:line-through;font-style:italic;">${{ number_format($item->fake_price, 2) }}</h5>
			<h3>${{ number_format($item->price, 2) }}</h3>
		</div>
		<div class="col-sm-3">
			<h4>Quantity</h4>
			<hr />
			{{ $item->qty }}
		</div>
	</div>
	<hr />
@endforeach
<div class="text-right">
	<h3>Total: ${{ number_format($order->total, 2) }}</h3>
</div>