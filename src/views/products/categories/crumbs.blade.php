<ol class="breadcrumb">
	<?php $i = 0; ?>
	<?php $url_original = $url; ?>
	@foreach ($crumbs as $id=>$category)
		@if (++$i < count($crumbs))
			<li>
				<?php
					$url = str_replace('{id}', $id, $url_original);
					$url = str_replace('{slug}', $category->slug, $url);
				?>
				<a href="{{ $url }}">
					{{ $category->name }}
				</a>
			</li>
		@else
			<li class="active">
				{{ $category->name }}
			</li>
		@endif
	@endforeach
</ol>