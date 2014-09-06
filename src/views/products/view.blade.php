@extends('core::template')

@section('title', $product->name)

@section('meta')
	<meta name="og:title" content="{{ $product->name }}" />
	<meta name="twitter:title" content="{{ $product->name }}" />
	<meta name="description" content="{{ $product->plaintext }}" />
	<meta name="og:description" content="{{ $product->plaintext }}" />
	<meta name="twitter:description" content="{{ $product->plaintext }}" />
	<meta name="og:url" content="{{ $product->link() }}" />
	<meta name="twitter:url" content="{{ $product->link() }}" />
	<meta name="og:type" content="" />
	@if ($product->images->count())
		<meta name="og:image" content="{{ asset($product->images[0]->thumb) }}" />
		<meta name="twitter:card" content="{{ asset($product->images[0]->thumb) }}" />
		<meta name="twitter:image" content="{{ asset($product->images[0]->thumb) }}" />
	@endif
@stop

@section('css')
	<style>
		#fakePriceWrap {
			text-decoration:line-through;
			font-style:italic;
		}
	</style>
@stop

@section('js')
	<script>
		$(function() {
			var qty              = {{ $product->qty }};
			var inventory        = {{ $product->inventory }};
			var productPrice     = {{ $product->price }};
			var productFakePrice = {{ $product->fake_price }};
			var options          = {{ json_encode($options) }};

			function showPrice() {
				var price     = productPrice;
				var fakePrice = productFakePrice;
				$('.optionSelect').each(function() {
					var optionPrice = parseFloat(options[$(this).val()]['price']);
					qty             = options[$(this).val()]['qty'];
					price          += optionPrice;
					fakePrice      += optionPrice;
				});
				$('#price').html(price.toFixed(2));
				$('#fakePrice').html(fakePrice.toFixed(2));
			}
			showPrice();

			$('.optionSelect').change(function() {
				showPrice();
				handleQty();
			});

			function handleQty() {
				if (!inventory) return;
				if (qty && $('#qty').val() > qty) {
					$('#qty').val(qty);
					alert('There are only '+qty+' of these available for purchase.');
				}
				if (qty) {
					$('.inventoryShow').show();
					$('.inventoryHide').hide();
				} else {
					$('.inventoryShow').hide();
					$('.inventoryHide').show();
				}
				if (qty && qty < 10) {
					$('#onlyLeft').show().find('span').html(qty);
				} else {
					$('#onlyLeft').hide();
				}
			}
			handleQty();

			$('#qty').change(function() {
				if (!parseInt($(this).val())) $(this).val(1);
				handleQty();
			});

			$('.qtyPlus').click(function() {
				var $qty = $(this).prev();
				adjustQuantity($qty, 1);
			});
			$('.qtyMinus').click(function() {
				var $qty = $(this).next();
				adjustQuantity($qty, -1);
			});

			function adjustQuantity($qty, by) {
				var qty = $qty.val();
				var qtyNew = parseInt($qty.val()) + by;
				qtyNew = (qtyNew) ? qtyNew : 1;
				if (qty == qtyNew) return;
				$qty.val(qtyNew).trigger('change');
			}

			$('.addToCart').click(function() {
				$(this).addClass('disabled').html('<span class="glyphicon glyphicon-shopping-cart"></span> Adding to cart...');
			});
		});
	</script>
@stop

@section('content')
	<div class="row">
		<div class="col-sm-6">
			@foreach ($product->images as $image)
				<a class="fancybox" href="{{ $image->src() }}">
					<img src="{{ $image->src() }}" style="width:100%" />
				</a>
			@endforeach
		</div>
		<div class="col-sm-6">
			{{ $crumbs }}
			<h1 style="margin:0 0 10px;">{{ $product->name }}</h1>
			{{ $product->html }}
			<hr />
			@if ($product->fake_price > 0)
				<h5 id="fakePriceWrap">$<span id="fakePrice"></span></h5>
			@endif
			<h3>$<span id="price"></span></h3>
			{{ Form::open(array('url'=>'cart-add')) }}
				@foreach ($product->options as $option)
					<div class="form-group">
						{{ Form::label('options['.$option->id.']', $option->name) }}
						{{ Form::select('options['.$option->id.']', $option->drop_down(), null, array('class'=>'form-control optionSelect')) }}
					</div>
				@endforeach
				{{ Form::hidden('product_id', $product->id) }}
				<p>
					{{ Form::label('qty', 'Quantity') }}
				</p>
				@if ($product->inventory)
					<p id="onlyLeft" class="inventoryShow">
						<i>Only <span></span> left!</i>
					</p>
					<p class="inventoryHide">
						<i>Out of stock!</i>
					</p>
				@endif
				<div class="form-group inventoryShow">
					<button type="button" class="btn btn-primary btn-xs qtyMinus">
						<span class="glyphicon glyphicon-minus"></span>
					</button>
					{{ Form::text('qty', 1, array('class'=>'form-control text-center', 'style'=>'display:inline-block;width:50px;')) }}
					<button type="button" class="btn btn-primary btn-xs qtyPlus">
						<span class="glyphicon glyphicon-plus"></span>
					</button>
				</div>
				{{ Form::hidden('product_id', $product->id) }}
				<button type="submit" class="btn btn-primary addToCart inventoryShow">
					<span class="glyphicon glyphicon-shopping-cart"></span>
					Add To Cart
				</button>
			{{ Form::close() }}
		</div>
	</div>
@stop