<?php

class AdminProductController extends AdminCrudController {

	public $model		= 'Product';
	public $plural		= 'products';
	public $singular	= 'product';
	public $package		= 'products';

	public function index()
	{

		return View::make($this->package . '::admin.' . $this->plural . '.index', $this->data);
	}


}