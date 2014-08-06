@extends('core::admin.template')

@section('title', ucfirst($action).' Product Category')

@section('css')
@stop

@section('js')
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Product Category</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>admin_uri('products/categories/delete/'.$category->id),
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this category (AND ALL NESTED PRODUCTS) forever?')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
		{{ Form::close() }}
	@endif
	<div class="row">
		<div class="col-sm-10">
			@if ($action == 'edit')
				{{ Form::model($category) }}
			@elseif ($action == 'add')
				{{ Form::open(array('role'=>'form', 'method'=>'post')) }}
			@endif

				@if (isset($menu_id))
					{{ Form::hidden('menu_id', $menu_id) }}
				@endif

				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								<span class="required">*</span>
								{{ Form::label('name', 'Name') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Name', 'required')) }}
								</div>
							</td>
						</tr>
						<tr>
							<td>
								{{ Form::label('image', 'Image') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::text('image', null, array('class'=>'form-control', 'placeholder'=>'Image')) }}
									<div class="text-right pad">
										<button type="button" class="btn btn-default imageBrowse">Browse...</button>
									</div>
								</div>
							</td>

						</tr>
					</tbody>
				</table>
				<div class="text-right pad">
					<input type="submit" class="btn btn-primary" value="Save" />
				</div>
			{{ Form::close() }}
		</div>
	</div>
@stop