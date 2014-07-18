<div class="panel panel-default option">
	<div class="panel-heading">
		<button type="button" class="btn btn-xs btn-default optionHandle">
			<span class="glyphicon glyphicon-resize-vertical"></span>
		</button>
		<input type="text" class="form-control form-control-inline optionName" placeholder="Option Group Name" />
		<button type="button" class="removeOption btn btn-xs btn-danger" style="float:right;">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</div>
	<div class="panel-body">
		<p>Options:</p>
		<div class="options">
			<div class="row optionItem">
				<div class="col-sm-4">
					<button type="button" class="btn btn-xs btn-default handle">
						<span class="glyphicon glyphicon-resize-vertical"></span>
					</button>
					<input type="text" class="form-control form-control-inline optionItemName" placeholder="Option Name" />
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control optionItemPrice" placeholder="Price Modifier" />
				</div>
				<div class="col-sm-5">
					<input type="text" class="form-control optionItemImage" placeholder="Image" />
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
		</div>
		<button type="button" class="btn btn-sm btn-primary addOptionItem">
			<span class="glyphicon glyphicon-plus"></span>
			Add Option
		</button>
	</div>
</div>