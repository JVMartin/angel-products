@extends('core::admin.template')

@section('title', 'Products')

@section('js')
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Product Categories</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('categories/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
	</div>
@stop