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

			//-----------
			// Images
			//-----------
			var $imageTR = $('#imagesTable tbody tr').last().clone();

			@if (isset($product) && $product->images->count())
				$('#imagesTable tbody tr').last().remove();
			@endif

			$("#imagesTable tbody").sortable(sortObj);

			$('#addImage').click(function() {
				$('#imagesTable tbody').append($imageTR.clone());
				bindImageBrowsers();
			});

			$('#imagesTable').on('click', '.removeImage', function() {
				$(this).closest('tr').remove();
				if ($('#imagesTable tbody').children('tr').length < 1) {
					$('#imagesTable tbody').append($imageTR.clone());
				}
			});

			//-----------
			// Options
			//-----------
			var $option = $('.option').last().clone();
			var $optionItem = $('.optionItem').last().clone();

			@if (isset($product) && $product->options->count())
				$('.option').last().remove();
			@endif

			$('#options').sortable({
				cancel: '',
				handle: '.optionHandle',
				stop: function(e, ui) {
					fixOptions();
				}
			});

			$('.options').sortable({
				cancel: '',
				handle: '.handle',
				stop: function(e, ui) {
					fixOptions();
				}
			});

			$('#options').on('click', '.removeOption', function() {
				$(this).closest('.option').remove();
				if ($('.option').length < 1) {
					$('#options').append($option.clone());
				}
				fixOptions();
			});
			$('#options').on('click', '.removeOptionItem', function() {
				var $tempOption = $(this).closest('.option');
				$(this).closest('.optionItem').remove();
				if ($tempOption.find('.optionItem').length < 1) {
					$tempOption.find('.options').append($optionItem.clone());
				}
				fixOptions();
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
					$(this).find('.optionID').attr('name', 'options['+optCount+'][id]');
					$(this).find('.optionName').attr('name', 'options['+optCount+'][name]');
					var optItemCount = 0;
					$(this).find('.optionItem').each(function() {
						$(this).find('.optionItemID').attr('name', 'options['+optCount+'][items]['+optItemCount+'][id]');
						$(this).find('.optionItemName').attr('name', 'options['+optCount+'][items]['+optItemCount+'][name]');
						$(this).find('.optionItemPrice').attr('name', 'options['+optCount+'][items]['+optItemCount+'][price]');
						$(this).find('.optionItemImage').attr('name', 'options['+optCount+'][items]['+optItemCount+'][image]');
						optItemCount++;
					});
					optCount++;
				});
			}
			fixOptions();

			$('#productForm').submit(function() {
				var notReady = false;
				if ($('.optionName').length > 1 || $('.optionItem').length > 1) {
					$('.optionName').each(function() {
						if (!$(this).val()) notReady = true;
					});
				}
				if (notReady) {
					if (!confirm('If you proceed, you will abandon any option groups that don\'t have names!  Proceed?')) return false;
				}
			});

			$('#relatedProductsTable').on('change', '.relatedCategory', function() {
				var $categoryID = $(this).val();
				var $tr = $(this).closest('tr');
				$tr.find('.relatedCategoryProducts').hide().prop('disabled', true);
				$tr.find('.relatedCategoryProducts.category' + $categoryID).show().prop('disabled', false);
			}).on('click', '.removeRelated', function() {
				$(this).closest('tr').remove();
			});

			$('.relatedCategory').trigger('change');

			var $relatedProduct = $('.relatedProduct').last().clone();
			$('.relatedProduct').last().remove();

			$('#addRelatedProduct').click(function() {
				$('#relatedProductsTable tbody').append($relatedProduct.clone());
			});

			$('#relatedProductsTable tbody').sortable(sortObj);

			var $category = $('.category').last().clone();
			$('.category').last().remove();

			$('#addCategory').click(function() {
				$('#categories').append($category.clone());
			});

			$('#categories').on('click', '.removeCategory', function() {
				$(this).closest('.category').remove();
			});
			@if ($action == 'edit')
				$('.removeCategory').first().remove();
			@endif

			$('form').submit(function() {
				$('#save').addClass('disabled').val('Saving...');
			});
		});
	</script>
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Product</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>'admin/products/delete/'.$product->id,
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this product forever?  This action cannot be undone!')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
		{{ Form::close() }}
	@endif

	@if ($action == 'edit')
		{{ Form::model($product, array('role'=>'form', 'id'=>'productForm')) }}
	@elseif ($action == 'add')
		{{ Form::open(array('role'=>'form', 'id'=>'productForm')) }}
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
								<b>Categories</b>
							</td>
							<td>
								<div id="categories">
									@if ($action == 'add')
										{{ Form::select('categories[]', $ProductCategory::drop_down_with($categories), Input::get('to_category'), array('class'=>'form-control', 'style'=>'width:auto;margin-bottom:10px;', 'required')) }}
									@else
										@foreach ($product->categories as $category)
											@include('products::admin.products.category-inputs')
										@endforeach
									@endif
									<?php unset($category); ?>
									@include('products::admin.products.category-inputs')
								</div>
								<button id="addCategory" type="button" class="btn btn-sm btn-primary">
									<span class="glyphicon glyphicon-plus"></span>
									Add Product To Another Category
								</button>
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
						<tr>
							<td>
								<b>Related Products</b>
							</td>
							<td>
								<table id="relatedProductsTable" class="table table-striped" style="width:100%;">
									<thead>
										<tr>
											<th></th>
											<th>Category</th>
											<th>Product</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@if (isset($product))
											@foreach ($product->related as $relatedProduct)
												@include('products::admin.products.related-inputs')
											@endforeach
										@endif
										<?php unset($relatedProduct); ?>
										@include('products::admin.products.related-inputs')
									</tbody>
								</table>
								<button id="addRelatedProduct" type="button" class="btn btn-sm btn-primary">
									<span class="glyphicon glyphicon-plus"></span>
									Add Related Product
								</button>
							</td>
						</tr>
						@if ($action == 'edit')
							<tr>
								<td>
								</td>
								<td>
									<div class="row">
										<div class="col-sm-6">
											<div class="expandBelow">
												<span class="glyphicon glyphicon-chevron-down"></span> Change Log
											</div>
											<div class="expander changesExpander">
												<?php $changes = $product->changes(); ?>
												@include('core::admin.changes.log')
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>{{-- Left Column --}}
		</div>{{-- Row --}}
		<div class="text-right pad">
			<input type="submit" class="btn btn-primary" value="Save" id="save" />
		</div>
	{{ Form::close() }}
@stop