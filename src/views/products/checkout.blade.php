@extends('core::template')

@section('title', 'Checkout')

@section('meta')
@stop

@section('css')
	<style>
		.required {
			color: #ff0000;
		}
	</style>
@stop

@section('js')
	{{ HTML::script('https://js.stripe.com/v2/') }}
	<script type="text/javascript">
		// This identifies your website in the createToken call below
	 	Stripe.setPublishableKey("{{ Config::get('products::stripe.' . $settings['stripe']['value'] . '.publishable') }}");

		$(function() {
			$('#submit').click(function() {
				$(this).prop('disabled', true).html('Submitting...');
				Stripe.card.createToken($('#payment-form'), stripeResponseHandler);
			});
		});

		function stripeResponseHandler(status, response) {
			if (response.error) {
				// Show the errors on the form
				$('#payment-errors').html('<div class="alert alert-danger">'+response.error.message+'</div>');
				$('#submit').prop('disabled', false).html('Submit Payment');
				return;
			}
			var token = response.id;
			$('#stripeToken').val(token);
			//$('#address-form').submit();
		};
	</script>
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
			<hr />
			<div id="payment-errors"></div>
			<form action="" method="POST" id="payment-form">
				<div class="form-group" style="width:400px;">
					<span class="required">*</span>
					{{ Form::label(null, 'Card Number') }}
					{{ Form::text(null, null, array('class'=>'form-control', 'placeholder'=>'Card Number', 'data-stripe'=>'number')) }}
				</div>
				<div class="form-group" style="width:100px;display:inline-block;">
					<span class="required">*</span>
					{{ Form::label(null, 'Exp. Month') }}
					{{ Form::text(null, null, array('class'=>'form-control', 'placeholder'=>'Exp. Month', 'data-stripe'=>'exp_month')) }}
				</div>
				<div class="form-group" style="width:100px;margin-left:15px;display:inline-block;">
					<span class="required">*</span>
					{{ Form::label(null, 'Exp. Year') }}
					{{ Form::text(null, null, array('class'=>'form-control', 'placeholder'=>'Exp. Year', 'data-stripe'=>'exp_year')) }}
				</div>
				<div class="form-group" style="width:100px;">
					<span class="required">*</span>
					{{ Form::label(null, 'CVC') }}
					{{ Form::text(null, null, array('class'=>'form-control', 'placeholder'=>'CVC', 'data-stripe'=>'cvc')) }}
				</div>
			</form>
			<form action="{{ url('checkout') }}" method="POST" id="address-form">
				<input type="hidden" id="stripeToken" nam="stripeToken" />
				<h3>Shipping Address</h3>
				<hr />
				<div class="form-group">
					<span class="required">*</span>
					{{ Form::label('shipping_address', 'Address') }}
					{{ Form::text('shipping_address', null, array('class'=>'form-control', 'placeholder'=>'Address')) }}
				</div>
				<div class="form-group">
					{{ Form::label('shipping_address_2', 'Address 2') }}
					{{ Form::text('shipping_address_2', null, array('class'=>'form-control', 'placeholder'=>'Address 2')) }}
				</div>
				<div class="form-group">
					<span class="required">*</span>
					{{ Form::label('shipping_city', 'City') }}
					{{ Form::text('shipping_city', null, array('class'=>'form-control', 'placeholder'=>'City')) }}
				</div>
				<div class="form-group">
					<span class="required">*</span>
					{{ Form::label('shipping_state', 'State') }}
					{{ Form::text('shipping_state', null, array('class'=>'form-control', 'placeholder'=>'State')) }}
				</div>
			</form>
			<button type="button" class="btn btn-default" id="submit">
				Submit Payment
			</button>
		</div>
	</div>
@stop