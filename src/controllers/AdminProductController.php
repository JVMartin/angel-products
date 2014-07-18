<?php namespace Angel\Products;

use Input, App, Redirect;

class AdminProductController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Product';
	protected $uri		= 'products';
	protected $plural	= 'products';
	protected $singular	= 'product';
	protected $package	= 'products';

	protected $slug     = 'name';

	public function add()
	{
		$productCategoryModel = App::make('ProductCategory');

		$this->data['categories'] = $productCategoryModel::orderBy('order')->get();
		return parent::add();
	}

	public function add_redirect($object)
	{
		return Redirect::to(admin_uri('products/categories/show-products/' . $object->category_id))->with('success', '
			<p>Product successfully created.</p>
		');
	}

	public function edit($id)
	{
		$productCategoryModel = App::make('ProductCategory');

		$this->data['categories'] = $productCategoryModel::orderBy('order')->get();
		return parent::edit($id);
	}

	public function edit_redirect($object)
	{
		return Redirect::to($this->uri('edit/' . $object->id))->with('success', '
			<p>Product successfully updated.</p>
			<p><a href="' . admin_url('products/categories/show-products/' . $object->category_id) . '">Return to index</a></p>
		');
	}

	public function validate_rules($id = null)
	{
		return array(
			'category_id' => 'required',
			'name'        => 'required'
		);
	}

	public function after_save($product, &$changes = array())
	{
		$ProductImage = App::make('ProductImage');
		$ProductOption = App::make('ProductOption');
		$ProductOptionItem = App::make('ProductOptionItem');

		$ProductImage::where('product_id', $product->id)->delete();
		$thumbs = Input::get('imageThumbs');
		foreach (Input::get('images') as $i=>$data_image) {
			if (!$data_image) continue;
			$image = new $ProductImage;
			$image->product_id	= $product->id;
			$image->image		= $data_image;
			$image->order		= $i;
			$image->thumb		= $thumbs[$i];
			$image->save();
		}

		$ProductOption::where('product_id', $product->id)->delete();
		foreach (Input::get('options') as $order=>$data_option) {
			if (!isset($data_option['name']) || !$data_option['name']) continue;
			$option = new $ProductOption;
			$option->product_id = $product->id;
			$option->order      = $order;
			$option->name       = $data_option['name'];
			$option->save();

			foreach ($data_option['items'] as $order=>$data_item) {
				if (!isset($data_item['name']) || !$data_item['name']) continue;
				$item = new $ProductOptionItem;
				$item->product_option_id = $option->id;
				$item->order             = $order;
				$item->name              = $data_item['name'];
				$item->price             = $data_item['price'];
				$item->image             = $data_item['image'];
				$item->save();
			}
		}
	}

}