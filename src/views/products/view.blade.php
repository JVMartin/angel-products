@extends('core::template')

@section('title', $product->name)

@section('meta')
@stop

@section('css')
@stop

@section('js')
	<script>
		$(function() {
			var productPrice = {{ number_format($product->price) }};
			var fakePrice = {{ number_format($product->fakePrice) }};
			var options = {{ json_encode($options) }};

			function showPrice() {
				var price = productPrice;
				$('.optionSelect').each(function() {
					price += parseFloat(options[$(this).val()]);
				});
				$('#price').html(price.toFixed(2));
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
		<div class="col-sm-6">
			@foreach ($product->images as $image)
				<img src="{{ $image->src() }}" style="width:100%" />
			@endforeach
		</div>
		<div class="col-sm-6">
			{{ $crumbs }}
			{{ $product->description }}
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