@extends('core::admin.template')

@section('title', 'View Order')

@section('meta')
@stop

@section('css')
@stop

@section('js')
	<script>
		$(function() {
			$('#send-tracking').submit(function(e) {
				if (!confirm('Really send tracking number?')) e.preventDefault();
			});
		});
	</script>
@stop

@section('content')
	<h1>Order Detail</h1>
	<div class="row">
		<div class="col-xs-12">
			{{ Form::open(array('url'=>admin_uri('orders/mark-shipped/' . $order->id), 'role'=>'form')) }}
				<p>
					Shipping Status:
					@if ($order->shipped)
						<span class="glyphicon glyphicon-ok" style="color:#008000"></span> Shipped
					@else
						<span class="glyphicon glyphicon-remove" style="color:#ff0000"></span> Not Shipped
					@endif
				</p>
				@if ($order->shipped)
					<button type="submit" class="btn btn-default">
						Mark as Not Shipped
					</button>
				@else
					<button type="submit" class="btn btn-default" name="mark-shipped" value="1">
						Mark as Shipped
					</button>
				@endif
			{{ Form::close() }}
		</div>
	</div>
	<hr />
	<div class="row">
		<div class="col-sm-6">
		{{ Form::model($order, array('url'=>admin_uri('orders/tracking/' . $order->id), 'role'=>'form')) }}
			<p>
				Tracking Number:
			</p>
			<div class="form-group">
				{{ Form::text('tracking', null, array('class'=>'form-control', 'placeholder'=>'Tracking Number')) }}
			</div>
			<button type="submit" class="btn btn-default" name="mark-shipped" value="1">
				Save Tracking Number
			</button>
		{{ Form::close() }}
		</div>
	</div>
	<hr />
	<div class="row" style="margin-top:12px;">
		<div class="col-sm-6">
		{{ Form::open(array('url'=>admin_uri('orders/send-tracking/' . $order->id), 'role'=>'form', 'id'=>'send-tracking')) }}
			<p>
				Tracking Number Emailed:
				@if ($order->tracking_sent)
					<span class="glyphicon glyphicon-ok" style="color:#008000"></span> Yes
				@else
					<span class="glyphicon glyphicon-remove" style="color:#ff0000"></span> No
				@endif
			</p>
			<button type="submit" class="btn btn-default" name="mark-shipped" value="1">
				Email Tracking Number
			</button>
		{{ Form::close() }}
		</div>
	</div>
	<hr />
	@include('products::orders.details')
@stop