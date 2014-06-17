@foreach ($categories as $category)
	<li>
		<div>
			{{ $category }}
		</div>
		@if (isset($category->children))
			<ol>
				@include('products::admin.products.categories.render', array('categories', $category->children))
			</ol>
		@endif
	</li>
@endforeach