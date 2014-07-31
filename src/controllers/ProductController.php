<?php namespace Angel\Products;

use Config, App, View, Input, Redirect, Validator, ToolBelt, Session, Auth;
use Stripe, Stripe_Charge, Stripe_CardError;

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

		if (!Input::get('stripeToken')) {
			return 'The Stripe token was not generated correctly.';
		}

		$validator = Validator::make(Input::all(), array(
			'shipping_name'    => 'required',
			'shipping_address' => 'required',
			'shipping_city'    => 'required',
			'shipping_state'   => 'required|size:2',
			'shipping_zip'     => 'required',
		));
		if ($validator->fails()) {
			$errors = '';
			foreach($validator->messages()->all() as $error) {
				$errors .= '<p>' . $error . '</p>';
			}
			return $errors;
		}

		Stripe::setApiKey(Config::get('products::stripe.' . $this->settings['stripe']['value'] . '.secret'));

		try {
			$charge = Stripe_Charge::create(array(
				'amount'   => ToolBelt::pennies($Cart->total()),
				'currency' => 'usd',
				'card'     => Input::get('stripeToken')
			));
		} catch (Stripe_CardError $e) {
			return $e->getMessage();
		}

		$Order = App::make('Order');

		$order = new $Order;
		$order->charge_id = $charge->id;
		$order->total = $Cart->total();

		if (Input::get('billing_zip')) {
			$billing = array(
				'name'      => Input::get('billing_name'),
				'address'   => Input::get('billing_address'),
				'address_2' => Input::get('billing_address_2'),
				'city'      => Input::get('billing_city'),
				'state'     => Input::get('billing_state'),
				'zip'       => Input::get('billing_zip'),
			);
			$order->billing_address = json_encode($billing);
		}

		$shipping = array(
			'name'      => Input::get('shipping_name'),
			'address'   => Input::get('shipping_address'),
			'address_2' => Input::get('shipping_address_2'),
			'city'      => Input::get('shipping_city'),
			'state'     => Input::get('shipping_state'),
			'zip'       => Input::get('shipping_zip'),
		);
		$order->shipping_address = json_encode($shipping);
		$charge->metadata['shipping'] = json_encode($shipping);

		if (Auth::check()) {
			$order->user_id = Auth::user()->id;
		}

		$order->cart = json_encode($Cart->all());
		$order->save();

		$charge->metadata['order_id'] = $order->id;
		$charge->save();

		Session::put('just-ordered', $order->id);
		$Cart->destroy();

		return 1;
	}

	public function order_summary()
	{
		if (!Session::get('just-ordered')) {
			return Redirect::to('/');
		}

		$Order = App::make('Order');

		$order = $Order->findOrFail(Session::get('just-ordered'));

		$this->data['order']            = $order;
		$this->data['cart']             = json_decode($order->cart);
		$this->data['billing_address']  = json_decode($order->billing_address);
		$this->data['shipping_address'] = json_decode($order->shipping_address);
		$this->data['shipping_address'] = json_decode($order->shipping_address);

		return View::make('products::orders.summary', $this->data);
	}

}