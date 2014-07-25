<?php namespace Angel\Products;

use App, View, Input, Redirect;

class ProductController extends \Angel\Core\AngelController {

	public function view($slug)
	{
		$Product         = App::make('Product');
		$ProductCategory = App::make('ProductCategory');

		$product = $Product::with('images', 'options')->where('slug', $slug)->firstOrFail();
		$categories = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();

		$options = array();
		foreach ($product->options as $option) {
			foreach ($option->items as $item) {
				$options[$item->id] = $item->price;
			}
		}

		$this->data['product'] = $product;
		$this->data['options'] = $options;
		$this->data['crumbs'] = $ProductCategory::crumbs($categories, $product->category_id, url('products/categories/{slug}'));

		return View::make('products::products.view', $this->data);
	}

	public function cart_add()
	{
		$Cart    = App::make('Cart');
		$Product = App::make('Product');

		$product = $Product::findOrFail(Input::get('product_id'));

		foreach (Input::get('options') as $option_item_id) {
			$product->markSelectedOption($option_item_id);
		}

		$Cart->add($product, Input::get('qty'));

		return Redirect::back()->with('success', 'This product has been added to your cart!');
	}

}