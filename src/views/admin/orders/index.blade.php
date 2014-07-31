@extends('core::admin.template')

@section('title', 'Orders')

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Orders</h1>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:80px;"></th>
						<th style="width:80px;">ID</th>
						<th>Email</th>
						<th>Total</th>
						<th>Shipped</th>
					</tr>
				</thead>
				<tbody>
					@foreach($orders as $order)
						<tr>
							<td>
								<a href="{{ admin_url('orders/show/' . $order->id) }}" class="btn btn-xs btn-info">
									<span class="glyphicon glyphicon-eye-open"></span>
								</a>
							</td>
							<td>{{ $order->id }}</td>
							<td>{{ $order->email }}</td>
							<td>${{ number_format($order->total, 2) }}</td>
							<td>
								@if ($order->shipped)
									<span class="glyphicon glyphicon-ok" style="color:#008000"></span>
								@else
									<span class="glyphicon glyphicon-remove" style="color:#ff0000"></span>
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
@stop