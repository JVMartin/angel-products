<tr class="relatedProduct">
	<td>
		<button type="button" class="btn btn-xs btn-default handle">
			<span class="glyphicon glyphicon-resize-vertical"></span>
		</button>
	</td>
	<td>
		{{ Form::select(null, $categories->lists('name', 'id'), isset($relatedProduct) ? $relatedProduct->categories[0]->id : null, array('class'=>'form-control relatedCategory', 'style'=>'width:auto;')) }}
	</td>
	<td>
		@foreach ($categories as $category)
			@if ($category->products->count())
				{{ Form::select('related[]', $category->products->lists('name', 'id'), isset($relatedProduct) ? $relatedProduct->id : null, array('class'=>'form-control relatedCategoryProducts category' . $category->id, 'style'=>'width:auto;display:none;')) }}
			@endif
		@endforeach
	</td>
	<td>
		<button type="button" class="removeRelated btn btn-xs btn-danger">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</td>
</tr>