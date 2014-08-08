<tr class="relatedProduct">
	<td>
		<button type="button" class="btn btn-xs btn-default handle">
			<span class="glyphicon glyphicon-resize-vertical"></span>
		</button>
	</td>
	<td>
		{{ Form::select(null, $ProductCategory::drop_down_with($categories), isset($relatedProduct) ? $relatedProduct->category : null, array('class'=>'form-control', 'style'=>'width:auto;')) }}
	</td>
	<td>
		@foreach ($categories as $category)
			{{ Form::select(null, $Product::drop_down_with($category->products), isset($relatedProduct) ? $relatedProduct->category : null, array('class'=>'form-control categoryProducts category' . $category->id, 'style'=>'width:auto;display:none;')) }}
		@endforeach
	</td>
	<td>
		<button type="button" class="removeRelated btn btn-xs btn-danger">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</td>
</tr>