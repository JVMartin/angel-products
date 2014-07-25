@extends('core::template')

@section('title', 'View Cart')

@section('meta')
@stop

@section('css')
	<style>
		.fakePriceWrap {
			text-decoration:line-through;
			font-style:italic;
		}
	</style>
@stop

@section('js')
	<script>
	</script>
@stop

@section('content')
	@foreach (Session::get('cart') as $key=>$item)
		<?php
			$product = json_decode($item['product']);
		?>
		<div class="row">
			<div class="col-sm-4">
				<img src="{{ $product->images[0]->image }}" style="width:100%" />
			</div>
			<div class="col-sm-4">
				<h4>{{ $product->name }}</h4>
				<hr />
				@foreach ($Cart->getOptions($key) as $group_name=>$option)
					<p>
						<b>{{ $group_name }}:</b>
					</p>
					<p>
						{{ $option->name }}
					</p>
				@endforeach
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					{{ Form::label('qty', 'Quantity') }}
					{{ Form::text('qty', 1, array('class'=>'form-control text-center', 'style'=>'display:inline-block;width:50px;')) }}
				</div>
			</div>
		</div>
		<hr />
	@endforeach
@stop