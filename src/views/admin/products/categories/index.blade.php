@extends('core::admin.template')

@section('title', 'Products')

@section('css')
	{{ HTML::style('packages/angel/products/nested.css') }}
@stop

@section('js')
	{{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
	{{ HTML::script('packages/angel/products/jquery.nested.js') }}
	<script>
		$(function() {
			$('.nested').nestedSortable({
				handle: 'div',
				items: 'li',
				toleranceElement: '> div',
				stop: function(e, ui) {
					var tree = $('.nested').nestedSortable('serialize');
					$('#treeInput').val(tree);
				}
			});
		});
	</script>
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
			@if (!count($categories))
				<div class="alert alert-info">
					Start by creating at least one product category.<br />
					Then, you will be able to add products to that category.
				</div>
			@endif
			<ol class="nested">
				@include('products::admin.products.categories.tree')
			</ol>
		</div>
	</div>
	@if (count($categories))
		<div class="row pad">
			<div class="col-xs-12">
				{{ Form::open(array('url'=>admin_url('products/categories/update-tree'))) }}
					<input type="hidden" name="tree" id="treeInput" />
					<button type="submit" class="btn btn-sm btn-primary">
						Save Tree
					</button>
				{{ Form::close() }}
			</div>
		</div>
	@endif
@stop