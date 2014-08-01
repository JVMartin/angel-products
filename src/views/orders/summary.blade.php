@extends('core::template')

@section('title', 'Order Summary')

@section('content')
	<h1>Order Summary</h1>
	<p>Thank you for your order!</p>
	<p>Here is your receipt.  You will also be emailed a receipt.</p>
	@include('products::orders.details')
@stop