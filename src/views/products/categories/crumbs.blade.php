<ol class="breadcrumb">
	<?php $i = 0; ?>
	@foreach ($crumbs as $id=>$name)
		@if (++$i < count($crumbs))
			<li>
				<a href="{{ str_replace('{id}', $id, $url) }}">
					{{ $name }}
				</a>
			</li>
		@else
			<li class="active">
				{{ $name }}
			</li>
		@endif
	@endforeach
</ol>