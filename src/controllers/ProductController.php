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

		$product = $Product::with('images', 'options')->findOrFail(Input::get('product_id'));

		$product->markSelectedOptions(Input::get('options'));

		$Cart->add($product, Input::get('qty'));

		return Redirect::back()->with('success', array(
			'This product has been added to your cart!',
			'<a href="' . url('cart') . '">View Cart</a>'
		));
	}

	public function cart_qty()
	{
		$Cart = App::make('Cart');

		foreach (Input::get('qty') as $key=>$qty) {
			$Cart->quantity($key, $qty);
		}

		return $Cart->total();
	}

	public function cart()
	{
		$Cart = App::make('Cart');

		$this->data['Cart'] = $Cart;

		return View::make('products::products.cart', $this->data);
	}

}