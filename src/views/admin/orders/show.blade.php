@extends('core::admin.template')

@section('title', 'View Order')

@section('meta')
@stop

@section('css')
@stop

@section('js')
@stop

@section('content')
	<div class="row">
		<div class="col-xs-12">
			<p>
				<b>Order ID:</b>
			</p>
			<p>
				{{ $order->id }}
			</p>
			<p>
				<b>Shipping To:</b>
			</p>
			<p>{{ $shipping_address->name }}</p>
			<p>{{ $shipping_address->address }}</p>
			@if ($shipping_address->address_2)
				<p>{{ $shipping_address->address_2 }}</p>
			@endif
			<p>
				{{ $shipping_address->city }}, {{ $shipping_address->state }}
				{{ $shipping_address->zip }}
			</p>
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
@stop