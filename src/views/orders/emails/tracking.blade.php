@extends('core::emails.template')

@section('content')
	<p>
		Your order #{{ $order->id }} has shipped!
	</p>
	<p>
		Your tracking number:
	</p>
	<p>
		{{ $order->tracking }}
	</p>
@stop