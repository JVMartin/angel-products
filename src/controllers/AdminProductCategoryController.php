<?php

class AdminProductCategoryController extends AdminCrudController {

	public $model		= 'ProductCategory';
	public $uri			= 'products';
	public $sub_uri		= 'categories';
	public $plural		= 'categories';
	public $singular	= 'category';
	public $package		= 'products';

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

}