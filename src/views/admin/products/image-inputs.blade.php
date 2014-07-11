<tr>
	<td>
		<button type="button" class="btn btn-xs btn-default handle">
			<span class="glyphicon glyphicon-resize-vertical"></span>
		</button>
	</td>
	<td>
		<input name="images[]" class="form-control" placeholder="Image" value="{{ isset($image) ? $image->image : '' }}" />
		<div class="text-right top-nudge">
			<button type="button" class="btn btn-default imageBrowse">Browse...</button>
		</div>
	</td>
	<td>
		<input name="imageThumbs[]" class="form-control" placeholder="Thumb" value="{{ isset($image) ? $image->thumb : '' }}" />
		<div class="text-right top-nudge">
			<button type="button" class="btn btn-default imageBrowse">Browse...</button>
		</div>
	</td>
	<td>
		<button type="button" class="removeImage btn btn-xs btn-danger">
			<span class="glyphicon glyphicon-remove"></span>
		</button>
	</td>
</tr>