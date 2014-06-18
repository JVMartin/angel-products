@foreach ($categories as $category)
	<li id="category_{{ $category->id }}">
		<div>
			<a class="btn btn-xs btn-default" href="">
				<span class="glyphicon glyphicon-edit"></span>
			</a>
			<a class="btn btn-xs btn-info" href="">
				<span class="glyphicon glyphicon-eye-open"></span>
			</a>
			{{ $category->name }}
		</div>
		@if (isset($category->children))
			<ol>
				@include('products::admin.products.categories.render', array('categories' => $category->children))
			</ol>
		@endif
	</li>
@endforeach