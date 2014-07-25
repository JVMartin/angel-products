@extends('core::template')

@section('title', $product->name)

@section('meta')
@stop

@section('css')
	<style>
		#fakePriceWrap {
			text-decoration:line-through;
			font-style:italic;
			@if ($product->fake_price == 0)
				display:none;
			@endif
		}
	</style>
@stop

@section('js')
	<script>
		$(function() {
			var productPrice     = {{ number_format($product->price) }};
			var productFakePrice = {{ number_format($product->fake_price) }};
			var options          = {{ json_encode($options) }};

			function showPrice() {
				var price     = productPrice;
				var fakePrice = productFakePrice;
				$('.optionSelect').each(function() {
					var optionPrice = parseFloat(options[$(this).val()]);
					price          += optionPrice;
					fakePrice      += optionPrice;
				});
				$('#price').html(price.toFixed(2));
				$('#fakePrice').html(fakePrice.toFixed(2));
			}
			showPrice();

			$('.optionSelect').change(function() {
				showPrice();
			});
		});
	</script>
@stop

@section('content')
	<div class="row">
		<div class="col-xs-12">

		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			@foreach ($product->images as $image)
				<img src="{{ $image->src() }}" style="width:100%" />
			@endforeach
		</div>
		<div class="col-sm-6">
			{{ $crumbs }}
			<h1 style="margin:0 0 10px;">{{ $product->name }}</h1>
			<p>
				{{ $product->description }}
			</p>
			<hr />
			<h5 id="fakePriceWrap">$<span id="fakePrice"></span></h5>
			<h3>$<span id="price"></span></h3>
			@foreach ($product->options as $option)
				<div class="form-group">
					{{ Form::label('options['.$option->id.']', $option->name) }}
					{{ Form::select('options['.$option->id.']', $option->drop_down(), null, array('class'=>'form-control optionSelect')) }}
				</div>
			@endforeach
		</div>
	</div>
@stop