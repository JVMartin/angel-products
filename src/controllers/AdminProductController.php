<?php

class AdminProductController extends AdminCrudController {

	public $model		= 'Product';
	public $uri			= 'products';
	public $sub_uri		= '';
	public $plural		= 'products';
	public $singular	= 'product';
	public $package		= 'products';

	public function index()
	{

		return View::make($this->view('index'), $this->data);
	}


}