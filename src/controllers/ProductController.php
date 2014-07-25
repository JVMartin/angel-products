<?php namespace Angel\Products;

use App, View;

class ProductController extends \Angel\Core\AngelController {

	public function view($slug)
	{
		$Product = App::make('Product');

		$this->data['product'] = $Product::with('images', 'options')->where('slug', $slug)->firstOrFail();

		return View::make('products::products.view', $this->data);
	}

}