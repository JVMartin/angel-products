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

	public function edit($id)
	{
		$this->data['categories'] = ProductCategory::orderBy('order')->get();
		return parent::edit($id);
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	public function after_save(&$product)
	{
		ProductImage::where('product_id', $product->id)->delete();
		$thumbs = Input::get('imageThumbs');
		foreach (Input::get('images') as $i=>$data_image) {
			$image = new ProductImage;
			$image->product_id	= $product->id;
			$image->image		= $data_image;
			$image->order		= $i;
			$image->thumb		= $thumbs[$i];
			$image->save();
		}
	}

}