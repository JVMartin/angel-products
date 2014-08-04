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
	{{ HTML::script('packages/angel/core/js/jquery/jquery.throttle-debounce.min.js') }}
	<script>
		$(function() {
			function qtyFormSubmit() {
				$.post($(this).attr('action'), $(this).serialize(), function(data) {
					$('#subtotal').html(data);
					$('#proceed').removeClass('disabled');
				}).fail(function() {
					alert('There was an error connecting to our servers.');
				});
			}

			$('#qtyForm').submit(function(e) {
				e.preventDefault();

				$('#subtotal').html('...');
				$('#proceed').addClass('disabled');
			}).submit($.debounce(500, qtyFormSubmit));

			$('.qty').change(function() {
				if (!parseInt($(this).val())) $(this).val(1);
				$('#qtyForm').submit();
			});
			/*.keyup(function() {
				$(this).trigger('change');
			})*/

			$('.qtyPlus').click(function() {
				var $qty = $(this).prev();
				adjustQuantity($qty, 1);
			});
			$('.qtyMinus').click(function() {
				var $qty = $(this).next();
				adjustQuantity($qty, -1);
			});

			function adjustQuantity($qty, by) {
				var qty = $qty.val();
				var qtyNew = parseInt($qty.val()) + by;
				qtyNew = (qtyNew) ? qtyNew : 1;
				if (qty == qtyNew) return;
				$qty.val(qtyNew).trigger('change');
			}

			$('.removeItem').click(function() {
				$(this).addClass('disabled').html('Removing item...');
			});
		});
	</script>
@stop

@section('content')
	<h1>Shopping Cart</h1>
	<hr />
	@if (!$Cart->all())
		<div class="alert alert-info">
			There are no items in your cart!
		</div>
	@endif
	{{ Form::open(array('id'=>'qtyForm', 'url'=>'cart-qty')) }}
		@foreach ($Cart->all() as $key=>$item)
			<?php $product = json_decode($item['product']); ?>
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
					<button type="button" class="btn btn-primary btn-xs qtyMinus">
						<span class="glyphicon glyphicon-minus"></span>
					</button>
					{{ Form::text('qty['.$key.']', $item['qty'], array('class'=>'form-control text-center qty', 'style'=>'display:inline-block;width:50px;')) }}
					<button type="button" class="btn btn-primary btn-xs qtyPlus">
						<span class="glyphicon glyphicon-plus"></span>
					</button>
					<div style="margin-top:15px;">
						<a href="{{ url('cart-remove/' . urlencode($key)) }}" class="btn btn-xs btn-default removeItem">
							Remove This Item
						</a>
					</div>
				</div>
			</div>
			<hr />
		@endforeach
	{{ Form::close() }}
	@if ($Cart->all())
		<div class="row">
			<div class="col-xs-12 text-right">
				<h3>Subtotal: $<span id="subtotal">{{ number_format($Cart->total(), 2) }}</span></h3>
				<a id="proceed" class="btn btn-primary" href="{{ url('checkout') }}">
					Proceed to Checkout
					<span class="glyphicon glyphicon-arrow-right"></span>
				</a>
			</div>
		</div>
	@endif
@stop