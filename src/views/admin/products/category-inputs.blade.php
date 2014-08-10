<div class="category" style="margin-bottom:10px;">
	{{ Form::select('categories[]', $ProductCategory::drop_down_with($categories), isset($category) ? $category->id : null, array('class'=>'form-control', 'style'=>'display:inline-block;width:auto;')) }}
	<button type="button" class="btn btn-xs btn-danger removeCategory">
		<span class="glyphicon glyphicon-remove"></span>
	</button>
</div>