@extends('admin.template')

@section('title', 'Products')

@section('js')
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Products</h1>
			<a class="btn btn-sm btn-primary" href="{{ url('admin/products/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
		<div class="col-sm-4 well">
			{{ Form::open(array('role'=>'form', 'method'=>'get')) }}
				<div class="form-group">
					<label>Search</label>
					<input type="text" name="search" class="form-control" value="{{ Input::get('search') }}" />
				</div>
				<div class="form-group">
					<label>Category</label>
					{{ Form::select('category_id', array(''=>'All') + $categories_drop, Input::get('category_id'), array('id'=>'categoryDrop', 'class'=>'form-control')) }}
				</div>
				<div class="form-group">
					<label>Subcategory</label>
					<br />
					<span id="noSubcategories"><i>None</i></span>
					@foreach ($subcategories_drops as $category_id=>$subcategories_drop)
						{{ Form::select('subcategory_id', array(''=>'All') + $subcategories_drop, Input::get('subcategory_id'), array('id'=>'subcategoryDrop'.$category_id, 'class'=>'form-control subcategoryDrop')) }}
					@endforeach
				</div>
				<div class="text-right">
					<input type="submit" class="btn btn-primary" value="Search" />
				</div>
			{{ Form::close() }}
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
	<div class="row">
		<div class="col-sm-10">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:80px;"></th>
						<th style="width:80px;">ID</th>
						<th>Name</th>
						<th>Image</th>
					</tr>
				</thead>
				<tbody>
				@foreach($products as $product)
					<tr{{ $product->deleted_at ? ' class="deleted"' : '' }}>
						<td>
							<a href="{{ url('admin/products/edit/' . $product->id) }}" class="btn btn-xs btn-default">
								<span class="glyphicon glyphicon-edit"></span>
							</a>
						</td>
						<td>{{ $product->id }}</td>
						<td>{{ $product->name }}</td>
						<td>
							<img src="{{ $product->images->first()->thumb }}" style="width:150px;" />
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
@stop