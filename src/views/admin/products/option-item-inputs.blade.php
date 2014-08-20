<div class="row optionItem">
	<div class="col-sm-4">
		<button type="button" class="btn btn-xs btn-default handle">
			<span class="glyphicon glyphicon-resize-vertical"></span>
		</button>
		<input type="hidden" class="optionItemID" value="{{ isset($optionItem) ? $optionItem->id : '' }}" />
		<input type="text" class="form-control form-control-inline optionItemName" placeholder="Option Name" value="{{ isset($optionItem) ? $optionItem->name : '' }}" />
	</div>
	<div class="col-sm-2">
		<input type="text" class="form-control optionItemPrice" placeholder="Price Modifier" value="{{ isset($optionItem) ? $optionItem->price : '' }}" />
		<input type="text" class="form-control optionItemQty inventoryShow" placeholder="Qty" value="{{ isset($optionItem) ? $optionItem->qty : 20 }}" />
	</div>
	<div class="col-sm-5">
		<input type="text" class="form-control optionItemImage" placeholder="Image" value="{{ isset($optionItem) ? $optionItem->image : '' }}" />
		<div class="text-right top-nudge">
			<button type="button" class="btn btn-default imageBrowse">Browse...</button>
		</div>
	</div>
	<div class="col-sm-1">
		<button type="button" class="removeOptionItem btn btn-xs btn-danger">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</div>
</div>