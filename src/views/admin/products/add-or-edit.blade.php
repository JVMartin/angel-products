@extends('core::admin.template')

@section('title', ucfirst($action).' Product')

@section('css')
	<style>
		.handle, .optionHandle {
			cursor:ns-resize;
		}
		.optionItem {
			margin-bottom:7px;
		}
	</style>
@stop

@section('js')
	{{ HTML::script('packages/angel/core/js/ckeditor/ckeditor.js') }}
	<script>
		$.fn.bsTooltip = $.fn.tooltip.noConflict();
	</script>
	{{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
	<script>
		$(function() {
			$('.glyphicon-question-sign').bsTooltip();

			$("#imagesTable tbody").sortable(sortObj);

			var $imageTR = $('#imagesTable tbody tr').last().clone();

			@if (isset($product) && $product->images->count())
				$('#imagesTable tbody tr').last().remove();
			@endif

			$('#addImage').click(function() {
				$('#imagesTable tbody').append($imageTR.clone());
				bindImageBrowsers();
			});

			$('#imagesTable').on('click', '.removeImage', function() {
				var $tr = $(this).closest('tr');
				if ($tr.parent().children('tr').length == 1) {
					alert('Let\'s keep at least one.');
					return;
				}
				$(this).closest('tr').remove();
			});

			$('#options').sortable({
				cancel: '',
				handle: '.optionHandle',
				stop: function(e, ui) {
					fixOptions();
				}
			});

			$('#options').on('click', '.removeOption', function() {
				if ($('.option').length == 1) {
					alert('Let\'s keep at least one.');
					return;
				}
				$(this).closest('.option').remove();
				fixOptions();
			});

			$('#options').on('click', '.removeOptionItem', function() {
				var $optionItem = $(this).closest('.optionItem');
				if ($optionItem.parent().children('.optionItem').length == 1) {
					alert('Let\'s keep at least one.');
					return;
				}
				$optionItem.remove();
				fixOptions();
			});

			var $option = $('.option').last().clone();
			var $optionItem = $('.optionItem').last().clone();

			@if (isset($product) && $product->options->count())
				$('.option').last().remove();
			@endif

			$('.options').sortable({
				cancel: '',
				handle: '.handle',
				stop: function(e, ui) {
					fixOptions();
				}
			});
			$('#addOption').click(function() {
				$('#options').append($option.clone());
				fixOptions();
			});
			$('#options').on('click', '.addOptionItem', function() {
				$(this).prev().append($optionItem.clone());
				fixOptions();
			});

			function fixOptions() {
				var optCount = 0;
				$('.option').each(function() {
					console.log(optCount);
					$(this).find('.optionName').attr('name', 'options['+optCount+'][name]');
					var optItemCount = 0;
					$(this).find('.optionItem').each(function() {
						$(this).find('.optionItemName').attr('name', 'options['+optCount+'][items]['+optItemCount+'][name]');
						$(this).find('.optionItemPrice').attr('name', 'options['+optCount+'][items]['+optItemCount+'][price]');
						$(this).find('.optionItemImage').attr('name', 'options['+optCount+'][items]['+optItemCount+'][image]');
						optItemCount++;
					});
					optCount++;
				});
			}
			fixOptions();

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
			<div class="col-md-12">
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
								<span class="required">*</span>
								{{ Form::label('category_id', 'Category') }}
							</td>
							<td>
								<?php $ProductCategory = App::make('ProductCategory'); ?>
								{{ Form::select('category_id', $ProductCategory::drop_down($categories), Input::get('to_category'), array('id'=>'categoryDrop', 'class'=>'form-control', 'style'=>'width:auto;', 'required')) }}
							</td>
						</tr>
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
								<span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="When a fake price is entered, it is displayed above the real price with a slash through it."></span>
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
								<table id="imagesTable" class="table table-striped" style="width:100%;">
									<thead>
										<tr>
											<th></th>
											<th>Image</th>
											<th>Thumb</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@if (isset($product))
											@foreach ($product->images as $image)
												@include('products::admin.products.image-inputs')
											@endforeach
										@endif
										<?php unset($image); ?>
										@include('products::admin.products.image-inputs')
									</tbody>
								</table>
								<button id="addImage" type="button" class="btn btn-sm btn-primary">
									<span class="glyphicon glyphicon-plus"></span>
									Add Image
								</button>
							</td>
						</tr>
						<tr>
							<td>
								<b>Options</b>
							</td>
							<td>
								<div id="options">
									@if (isset($product))
										@foreach ($product->options as $option)
											@include('products::admin.products.option-inputs')
										@endforeach
									@endif
									<?php unset($option); ?>
									@include('products::admin.products.option-inputs')
								</div>
								<button id="addOption" type="button" class="btn btn-sm btn-primary">
									<span class="glyphicon glyphicon-plus"></span>
									Add Option Group
								</button>
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