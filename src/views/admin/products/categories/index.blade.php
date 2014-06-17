@extends('core::admin.template')

@section('title', 'Products')

@section('js')
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Product Categories</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('products/categories/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<ol class="sortable">
				@include('products::admin.products.categories.render')
			</ol>
		</div>
	</div>
@stop