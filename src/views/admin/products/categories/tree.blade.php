@foreach ($categories as $category)
	<li id="category_{{ $category->id }}">
		<div>
			<a class="btn btn-xs btn-default" href="{{ $category->link_edit() }}">
				<span class="glyphicon glyphicon-edit"></span>
			</a>
			<a class="btn btn-xs btn-info" href="{{ admin_url('products/categories/show-products/' . $category->id) }}">
				<span class="glyphicon glyphicon-eye-open"></span>
				Products
			</a>
			{{ $category->name }}
		</div>
		@if (isset($category->children))
			<ol>
				@include('products::admin.products.categories.tree', array('categories' => $category->children))
			</ol>
		@endif
	</li>
@endforeach