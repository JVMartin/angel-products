<?php namespace Angel\Products;

use App, View;

class ProductController extends \Angel\Core\AngelController {

	public function view($slug)
	{
		$Product = App::make('Product');
		$ProductCategory = App::make('ProductCategory');

		$product = $Product::with('images', 'options')->where('slug', $slug)->firstOrFail();
		$categories = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();

		$this->data['product'] = $product;
		$this->data['crumbs'] = $ProductCategory::crumbs($categories, $product->category_id, url('products/categories/{slug}'));

		return View::make('products::products.view', $this->data);
	}

}