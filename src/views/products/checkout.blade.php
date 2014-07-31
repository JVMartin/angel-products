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
				$('#address-errors').html('');
				doError($('#payment-errors'), response.error.message);
				return;
			}
			$('#payment-errors').html('');

			var token = response.id;
			$('#stripeToken').val(token);
			$.post('{{ url('checkout') }}', $('#address-form').serialize(), function(data) {
				if (data != 1) {
					doError($('#address-errors'), data);
					console.log(data);
				}
			}).fail(function() {
				doError($('#address-errors'), 'Could not connect to the server.');
			});
		};

		function doError($where, error) {
			$where.html('<div class="alert alert-danger">'+error+'</div>');
			$('#submit').prop('disabled', false).html('Submit Payment');
			$('html, body').stop().animate({
				scrollTop: $where.offset().top - 50
			}, 500);
		}
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
					{{ Form::text(null, null, array('id'=>'card', 'class'=>'form-control', 'placeholder'=>'Card Number', 'data-stripe'=>'number')) }}
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
			<div id="address-errors"></div>
			<form action="" method="POST" id="address-form">
				{{ Form::token() }}
				<input type="hidden" id="stripeToken" name="stripeToken" />
				<h3>Billing Address</h3>
				<hr />
				<div class="form-group">
					{{ Form::label('billing_name', 'Name') }}
					{{ Form::text('billing_name', null, array('class'=>'form-control', 'placeholder'=>'Name')) }}
				</div>
				<div class="form-group">
					{{ Form::label('billing_address', 'Address') }}
					{{ Form::text('billing_address', null, array('class'=>'form-control', 'placeholder'=>'Address')) }}
				</div>
				<div class="form-group">
					{{ Form::label('billing_address_2', 'Address 2') }}
					{{ Form::text('billing_address_2', null, array('class'=>'form-control', 'placeholder'=>'Address 2')) }}
				</div>
				<div class="form-group">
					{{ Form::label('billing_city', 'City') }}
					{{ Form::text('billing_city', null, array('class'=>'form-control', 'placeholder'=>'City')) }}
				</div>
				<div class="form-group" style="width:70px;">
					{{ Form::label('billing_state', 'State') }}
					{{ Form::text('billing_state', null, array('class'=>'form-control', 'placeholder'=>'State')) }}
				</div>
				<div class="form-group" style="width:120px;">
					{{ Form::label('billing_zip', 'Zip Code') }}
					{{ Form::text('billing_zip', null, array('class'=>'form-control', 'placeholder'=>'Zip Code')) }}
				</div>
				<h3>Shipping Address</h3>
				<hr />
				<div class="form-group">
					<span class="required">*</span>
					{{ Form::label('shipping_name', 'Name') }}
					{{ Form::text('shipping_name', null, array('class'=>'form-control', 'placeholder'=>'Name')) }}
				</div>
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
				<div class="form-group" style="width:70px;">
					<span class="required">*</span>
					{{ Form::label('shipping_state', 'State') }}
					{{ Form::text('shipping_state', null, array('class'=>'form-control', 'placeholder'=>'State')) }}
				</div>
				<div class="form-group" style="width:120px;">
					<span class="required">*</span>
					{{ Form::label('shipping_zip', 'Zip Code') }}
					{{ Form::text('shipping_zip', null, array('class'=>'form-control', 'placeholder'=>'Zip Code', 'required')) }}
				</div>
			</form>
			<button type="button" class="btn btn-info" id="submit">
				Submit Payment
			</button>
		</div>
	</div>
@stop