@extends('core::template')

@section('title', 'Checkout')

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
@stop

@section('content')
	<div class="row">
		<div class="col-sm-4">
			<div class="well">
				<h3>Order Summary</h3>
				<hr />
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Product</th>
							<th>Qty</th>
							<th>Price</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($Cart->all() as $key=>$item)
							<tr>
								<td>
									<?php $product = json_decode($item['product']); ?>
									{{ $product->name }}
									<?php
										$itemOptions = $Cart->getOptions($key);
										if (count($itemOptions)) {
											$options = array();
											foreach ($itemOptions as $option) {
												$options[] = $option->name;
											}
											echo '(' . implode($options, ', ') . ')';
										}
									?>
								</td>
								<td>
									{{$item['qty'] }} x
								</td>
								<td>
									${{ number_format($item['price'], 2) }}
								</td>
							</tr>
						@endforeach
						<tr>
							<td><b>Subtotal</b></td>
							<td></td>
							<td><b>${{ number_format($Cart->total(), 2) }}</b></td>
						</tr>
					</tbody>
				</table>
				<a href="{{ url('cart') }}" class="btn btn-default">
					<span class="glyphicon glyphicon-arrow-left"></span>
					Back To Cart
				</a>
			</div>
		</div>
		<div class="col-sm-8">
			<h1>Checkout</h1>
			<form action="{{ url('checkout') }}" method="POST">
				<script
					src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
					data-key="{{ Config::get('products::stripe.' . $settings['stripe']['value'] . '.publishable') }}"
					data-amount="{{ \ToolBelt::pennies($Cart->total()) }}"
					data-name="{{ $settings['title']['value'] }}"
					{{-- data-description="For Your Health" --}}
					@if (Auth::check())
						data-email="{{ Auth::user()->email }}"
					@endif
					>
				</script>
			</form>
		</div>
	</div>
@stop