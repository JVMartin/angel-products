@extends('core::admin.template')

@section('title', $category->name . ' Category')

@section('css')
	<style>
		.breadcrumb {
			margin-bottom:5px;
		}
	</style>
@stop

@section('content')
	<div class="row">
		<div class="col-xs-12 pad">
			{{ $crumbs }}
		</div>
	</div>
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1 style="margin-top:5px;">{{ $category->name }} Category</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('products/categories/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add Product
			</a>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
	<div class="row">
		<div class="col-xs-12">

		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
@stop