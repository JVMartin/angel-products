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
	@if ($category->children->count())
		<div class="row">
			<div class="col-xs-12">
				Subcategories:
				<?php $i = 0; ?>
				@foreach ($category->children as $child)
					<a href="{{ admin_url('products/categories/show-products/' . $child->id) }}">
						{{ $child->name }}
					</a>
					@if (++$i < $category->children->count())
						-
					@endif
				@endforeach
			</div>
		</div>
	@endif
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1 style="margin-top:5px;">{{ $category->name }}</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('products/add?to_category=' . $category->id) }}">
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
			@if (!$products->count())
				<div class="alert alert-info">
					No products exist in this category yet.
				</div>
			@else
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="width:80px;"></th>
							<th style="width:80px;">ID</th>
							<th>Name</th>
							<th>Price</th>
							<th>Image Thumb</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($products as $product)
							<tr>
								<td>
									<a href="{{ $product->link_edit() }}" class="btn btn-xs btn-default">
										<span class="glyphicon glyphicon-edit"></span>
									</a>
									<a href="{{ $product->link() }}" class="btn btn-xs btn-info" target="_blank">
										<span class="glyphicon glyphicon-eye-open"></span>
									</a>
								</td>
								<td>{{ $product->id }}</td>
								<td>{{ $product->name }}</td>
								<td>{{ $product->price }}</td>
								<td>
									@if ($product->images->count() && $product->images->first()->thumb)
										<img src="{{ asset($product->images->first()->thumb) }}" style="width:120px;" />
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			@endif
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
@stop