<div class="panel panel-default option">
	<div class="panel-heading">
		<button type="button" class="btn btn-xs btn-default optionHandle">
			<span class="glyphicon glyphicon-resize-vertical"></span>
		</button>
		<input type="hidden" class="optionID" value="{{ isset($option) ? $option->id : '' }}" />
		<input type="text" class="form-control form-control-inline optionName" placeholder="Option Group Name" value="{{ isset($option) ? $option->name : '' }}" />
		<button type="button" class="removeOption btn btn-xs btn-danger" style="float:right;">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</div>
	<div class="panel-body">
		<p>Options:</p>
		<div class="options">
			@if (isset($option) && $option->items && $option->items->count())
				@foreach ($option->items as $optionItem)
					@include('products::admin.products.option-item-inputs', array('optionItem'=>$optionItem))
				@endforeach
			@else
				@include('products::admin.products.option-item-inputs')
			@endif
		</div>
		<button type="button" class="btn btn-sm btn-primary addOptionItem">
			<span class="glyphicon glyphicon-plus"></span>
			Add Option
		</button>
	</div>
</div>