<?php namespace Angel\Products;

use Input, App, Redirect;

class AdminProductController extends \Angel\Core\AdminCrudController {

	protected $model	= 'Product';
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

	public function attempt_add()
	{
		$error = parent::attempt_add();

		return ($error) ? $error : Redirect::to();
	}

	public function edit($id)
	{
		$productCategoryModel = App::make('ProductCategory');

		$this->data['categories'] = $productCategoryModel::orderBy('order')->get();
		return parent::edit($id);
	}

	public function attempt_edit($id)
	{
		$model = App::make($this->model);

		$errors = $this->validate($custom, $id);
		if (count($errors)) {
			return Redirect::to($this->uri('edit/' . $id))->withInput()->withErrors($errors);
		}

		$object = $model::withTrashed()->findOrFail($id);
		foreach ($model::columns() as $column) {
			$object->{$column} = isset($custom[$column]) ? $custom[$column] : Input::get($column);
		}
		if (isset($this->slug) && $this->slug) {
			$object->slug = $this->slug($model, 'slug', $object->{$this->slug}, $id);
		}
		$object->save();

		if (method_exists($this, 'after_save')) $this->after_save($object);

		return Redirect::to($this->uri('edit/' . $id))->with('success', '
			<p>' . $this->model . ' successfully updated.</p>
			<p><a href="' . admin_url('products/categories/show-products/' . $object->category_id) . '">Return to index</a></p>
		');
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