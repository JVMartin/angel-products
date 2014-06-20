<?php namespace Angel\Products;

use Input, App;

class AdminProductController extends \Angel\Core\AdminCrudController {

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
		$productCategoryModel = App::make('ProductCategory');

		$this->data['categories'] = $productCategoryModel::orderBy('order')->get();
		return parent::add();
	}

	public function edit($id)
	{
		$productCategoryModel = App::make('ProductCategory');

		$this->data['categories'] = $productCategoryModel::orderBy('order')->get();
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
		$productImageModel = App::make('ProductImage');

		$productImageModel::where('product_id', $product->id)->delete();
		$thumbs = Input::get('imageThumbs');
		foreach (Input::get('images') as $i=>$data_image) {
			$image = new $productImageModel;
			$image->product_id	= $product->id;
			$image->image		= $data_image;
			$image->order		= $i;
			$image->thumb		= $thumbs[$i];
			$image->save();
		}
	}

}