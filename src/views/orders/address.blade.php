<p>{{ $address->name }}</p>
<p>{{ $address->address }}</p>
@if ($address->address_2)
	<p>{{ $address->address_2 }}</p>
@endif
<p>
	{{ $address->city }}, {{ $address->state }} {{ $address->zip }}
</p>