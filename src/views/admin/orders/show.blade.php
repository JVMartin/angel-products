@extends('core::admin.template')

@section('title', 'View Order')

@section('meta')
@stop

@section('css')
@stop

@section('js')
@stop

@section('content')
	<h1>Order Detail</h1>
	{{ Form::open(array('url'=>admin_uri('orders/mark-shipped/' . $order->id), 'class'=>'form form-inline')) }}
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
	<hr />
	@include('products::orders.details')
@stop