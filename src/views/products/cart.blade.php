@extends('core::template')

@section('title', 'View Cart')

@section('meta')
@stop

@section('css')
	<style>
		.fakePriceWrap {
			text-decoration:line-through;
			font-style:italic;
		}
	</style>
@stop

@section('js')
	<script>
		$(function() {
			$('#qtyForm').submit(function(e) {
				e.preventDefault();

				$.post($(this).attr('action'), $(this).serialize(), function(data) {
					if (data == 1) return;
					alert('There was an error connecting to our servers.');
					console.log(data);
				}).fail(function() {
					alert('There was an error connecting to our servers.');
				});
			});
		});
	</script>
@stop

@section('content')
	@if (!$Cart->all())
		<div class="alert alert-info">
			There are no items in your cart!
		</div>
	@endif
	{{ Form::open(array('id'=>'qtyForm', 'url'=>'cart-qty')) }}
		@foreach ($Cart->all() as $key=>$item)
			<?php
				$product = json_decode($item['product']);
			?>
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
					<h5 class="fakePriceWrap">${{ number_format($item['fake_price'], 2) }}</h5>
					<h3>${{ number_format($item['price'], 2) }}</h3>
				</div>
				<div class="col-sm-3">
					<h4>Quantity</h4>
					<hr />
					<div class="form-group">
						{{ Form::text('qty['.$key.']', $item['qty'], array('class'=>'form-control text-center', 'style'=>'display:inline-block;width:50px;')) }}
					</div>
				</div>
			</div>
			<hr />
		@endforeach
		<button>Heyo</button>
	{{ Form::close() }}
	@if ($Cart->all())
		<div class="row">
			<div class="col-xs-12 text-right">
				<h3>Subtotal: $<span id="subtotal"></span></h3>
				<a class="btn btn-primary">
					Proceed to Checkout
				</a>
			</div>
		</div>
	@endif
@stop