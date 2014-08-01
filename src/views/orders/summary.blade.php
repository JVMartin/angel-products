@extends('core::template')

@section('title', 'Order Summary')

@section('meta')
@stop

@section('css')
@stop

@section('js')
@stop

@section('content')
	<h1>Order Summary</h1>
	<p>You will also be emailed a summary.</p>
	@include('products::orders.details')
@stop