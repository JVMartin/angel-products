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
	@include('products::orders.details')
@stop