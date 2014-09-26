@extends('core::emails.template')

@section('content')
	<?php
	$cart             = json_decode($order->cart, true);
	$billing_address  = json_decode($order->billing_address);
	$shipping_address = json_decode($order->shipping_address);

	$TempCart = clone App::make('Cart');
	$TempCart->load($cart);
	?>
	<p>
		Thank you for your order!  Here is your receipt.
	</p>
	<p>
		<b>Order ID:</b>
	</p>
	<p>
		{{ $order->id }}
	</p>
	<p>
		<b>Order Time:</b>
	</p>
	<p>
		{{ $order->created_at }}
	</p>
	@if (count($billing_address))
		<p>
			<b>Billed To:</b>
		<p>
		@include('products::orders.address', array('address'=>$billing_address))
	@endif
	<p>
		<b>Shipping To:</b>
	</p>
	@include('products::orders.address', array('address'=>$shipping_address))
	<hr />
	@foreach ($cart as $key=>$item)
		<?php $product = json_decode($item['product']); ?>
		@if (isset($product->images) && count($product->images))
			<p>
				<img src="{{ $message->embed(public_path() . $product->images[0]->image) }}" style="width:240px" width="240" />
			</p>
		@endif
		<p>
			<a href="{{ url('products/' . $product->slug) }}">
				{{ $product->name }}
			</a>
			<?php
			$options = $TempCart->getOptions($key);
			if (count($options)) {
				echo '(';
				$i = 0;
				foreach ($options as $group_name=>$option) {
					echo $group_name . ': ' . $option->name;
					if (++$i < count($options)) echo ', ';
				}
				echo ')';
			}
			?>
			-
			${{ number_format($item['price'], 2) }}
			x
			{{ $item['qty'] }}
		</p>
		<hr />
	@endforeach
	<p class="text-right">
		<b>Total: ${{ number_format($order->total, 2) }}</b>
	</p>
@stop