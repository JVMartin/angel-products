<?php

class AdminProductController extends AdminCrudController {

	protected $model	= 'Product';
	protected $uri		= 'products';
	protected $plural	= 'products';
	protected $singular	= 'product';
	protected $package	= 'products';

	public function index()
	{
		return View::make($this->view('index'), $this->data);
	}

	public function add()
	{
		$this->data['categories'] = ProductCategory::orderBy('order')->get();
		return parent::add();
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}


}