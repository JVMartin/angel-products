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

		return number_format($Cart->total(), 2);
	}

	public function cart_remove($key)
	{
		$Cart = App::make('Cart');

		$Cart->remove(urldecode($key));

		return Redirect::to('cart');
	}

	public function cart()
	{
		$this->data['Cart'] = App::make('Cart');

		return View::make('products::products.cart', $this->data);
	}

	public function checkout()
	{
		$this->data['Cart'] = App::make('Cart');

		return View::make('products::products.checkout', $this->data);
	}

	public function pay()
	{
		$Cart = App::make('Cart');
		$Order = App::make('Order');

		if (!Input::get('stripeToken')) {
			return Redirect::to('checkout')
				           ->withInput()
				           ->withErrors('The Stripe token was not generated correctly.');
		}

		try {
			Stripe_Charge::create(array(
				'amount'   => \ToolBelt::pennies($Cart->total()),
				'currency' => 'usd',
				'card'     => Input::get('stripeToken')
			));
		} catch (Stripe_CardError $e) {
			return Redirect::to('checkout')
				           ->withInput()
				           ->withErrors($e->getMessage());
		}

		Session::flash('old-cart', Session::get('cart'));
		$Cart->destroy();

		return Redirect::to('order-summary');
	}

	public function order_summary()
	{
		return View::make('products::orders.summary', $this->data);
	}

}