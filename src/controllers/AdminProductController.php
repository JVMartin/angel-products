<?php namespace Angel\Products;

use App, Redirect;

class AdminProductController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Product';
	protected $uri		= 'products';
	protected $plural	= 'products';
	protected $singular	= 'product';
	protected $package	= 'products';

	public function add()
	{
		$ProductCategory = App::make('ProductCategory');

		$this->data['ProductCategory'] = $ProductCategory;
		$this->data['Product']         = App::make('Product');
		$this->data['categories']      = $ProductCategory::with('products')->orderBy('order')->get();
		return parent::add();
	}

	public function add_redirect($product)
	{
		return Redirect::to(admin_uri('products/categories/show-products/' . $product->categories->first()->id))
			           ->with('success', '<p>Product successfully created.</p>');
	}

	public function edit($id)
	{
		$ProductCategory = App::make('ProductCategory');

		$this->data['ProductCategory'] = $ProductCategory;
		$this->data['Product']         = App::make('Product');
		$this->data['categories']      = $ProductCategory::with('products')->orderBy('order')->get();
		return parent::edit($id);
	}

	public function edit_redirect($product)
	{
		return Redirect::to($this->uri('edit/' . $product->id))
			           ->with('success', '
			<p>Product successfully updated.</p>
			<p>
				<a href="' . admin_url('products/categories/show-products/' . $product->categories->first()->id) . '">
					Return to index
				</a>
			</p>
		');
	}

}
