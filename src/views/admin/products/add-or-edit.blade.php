@extends('core::admin.template')

@section('title', ucfirst($action).' Product')

@section('js')
	{{ HTML::script('packages/angel/core/js/ckeditor/ckeditor.js') }}
	{{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
	<script>
		$(function() {
			$("#imagesTable tbody").sortable(sortObj);

			var $imageTR = $('#imagesTable tbody tr').last().clone();

			@if (isset($product))
				$('#imagesTable tbody tr').last().remove();
			@endif

			$('#addImage').click(function() {
				$('#imagesTable tbody').append($imageTR.clone());
				bindImageBrowsers();
				bindRemoveImage();
			});

			function bindRemoveImage() {
				$('.removeImage').click(function() {
					$(this).closest('tr').remove();
				});
			}
			bindRemoveImage();
		});
	</script>
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Product</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>'admin/products/hard-delete/'.$product->id,
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this product forever?  This action cannot be undone!')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
		{{ Form::close() }}
	@endif

	@if ($action == 'edit')
		{{ Form::model($product) }}
	@elseif ($action == 'add')
		{{ Form::open(array('role'=>'form', 'method'=>'post')) }}
	@endif

	@if (isset($menu_id))
		{{ Form::hidden('menu_id', $menu_id) }}
	@endif

	<div class="row">
		<div class="col-md-10">
			<table class="table table-striped">
				<tbody>
					@if (Config::get('core::languages'))
						<tr>
							<td>
								{{ Form::label('language_id', 'Language') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::select('language_id', $language_drop, $active_language->id, array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
					@endif
					<tr>
						<td>
							{{ Form::label('category_id', 'Category') }}
						</td>
						<td>
							{{ Form::select('category_id', ProductCategory::drop_down($categories), Input::get('to_category'), array('id'=>'categoryDrop', 'class'=>'form-control', 'style'=>'width:auto;')) }}
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('name', 'Name') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Name')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('size', 'Size') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('size', null, array('class'=>'form-control', 'placeholder'=>'Size')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('price', 'Price') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('price', null, array('class'=>'form-control', 'placeholder'=>'Price')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('fake_price', 'Fake Price') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('fake_price', null, array('class'=>'form-control', 'placeholder'=>'Fake Price')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<b>New</b>
						</td>
						<td>
							{{ Form::hidden('new', 0) }}
							<label>
								{{ Form::checkbox('new', 1) }}
								Yes
							</label>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('description', 'Description') }}
						</td>
						<td>
							{{ Form::textarea('description', null, array('class'=>'ckeditor')) }}
						</td>
					</tr>
					<tr>
						<td>
							<b>Images</b>
						</td>
						<td>
							<table id="imagesTable" class="table" style="width:100%;">
								<thead>
									<tr>
										<th></th>
										<th>Image</th>
										<th>Thumb (240px square)</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@if (isset($product))
										@foreach ($product->images as $image)
											<tr>
												<td>
													<button type="button" class="btn btn-xs btn-default handle">
														<span class="glyphicon glyphicon-resize-vertical"></span>
													</button>
												</td>
												<td>
													<input name="images[]" class="form-control" placeholder="Image" value="{{ $image->image }}" />
													<div class="text-right pad">
														<button type="button" class="btn btn-default imageBrowse">Browse...</button>
													</div>
												</td>
												<td>
													<input name="imageThumbs[]" class="form-control" placeholder="Thumb" value="{{ $image->thumb }}" />
													<div class="text-right pad">
														<button type="button" class="btn btn-default imageBrowse">Browse...</button>
													</div>
												</td>
												<td>
													<button type="button" class="removeImage btn btn-xs btn-danger">
														<span class="glyphicon glyphicon-remove"></span>
													</button>
												</td>
											</tr>
										@endforeach
									@endif
									<tr>
										<td>
											<button type="button" class="btn btn-xs btn-default handle">
												<span class="glyphicon glyphicon-resize-vertical"></span>
											</button>
										</td>
										<td>
											<input name="images[]" class="form-control" placeholder="Image" />
											<div class="text-right pad">
												<button type="button" class="btn btn-default imageBrowse">Browse...</button>
											</div>
										</td>
										<td>
											<input name="imageThumbs[]" class="form-control" placeholder="Thumb" />
											<div class="text-right pad">
												<button type="button" class="btn btn-default imageBrowse">Browse...</button>
											</div>
										</td>
										<td>
											<button type="button" class="removeImage btn btn-xs btn-danger">
												<span class="glyphicon glyphicon-remove"></span>
											</button>
										</td>
									</tr>
								</tbody>
							</table>
							<button id="addImage" type="button" class="btn btn-sm btn-primary">
								<span class="glyphicon glyphicon-plus"></span>
								Add Image
							</button>
							@if ($action == 'add')
							@else
							@endif
						</td>
					</tr>
				</tbody>
			</table>
		</div>{{-- Left Column --}}
	</div>{{-- Row --}}
	<div class="text-right pad">
		<input type="submit" class="btn btn-primary" value="Save" />
	</div>
	{{ Form::close() }}
@stop