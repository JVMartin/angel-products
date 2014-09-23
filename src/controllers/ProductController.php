<?php namespace Angel\Products;

use Config, App, View, Input, Redirect, Validator, ToolBelt, Session, Auth, Mail;
use Stripe, Stripe_Charge, Stripe_CardError;

class ProductController extends \Angel\Core\AngelController {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->Cart = $this->data['Cart'] = App::make('Cart');
	}

	public function view($slug)
	{
		$Product         = App::make('Product');
		$ProductCategory = App::make('ProductCategory');

		$product    = $Product::with('images', 'options')->where('slug', $slug)->firstOrFail();
		$categories = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();

		$options = array();
		foreach ($product->options as $option) {
			foreach ($option->items as $item) {
				$options[$item->id]['price'] = $item->price;
				$options[$item->id]['qty']   = $item->qty;
			}
		}

		$this->data['product'] = $product;
		$this->data['options'] = $options;
		$this->data['crumbs']  = $ProductCategory::crumbs($categories, $product->categories()->first()->id, url('products/categories/{slug}'));

		return View::make('products::products.view', $this->data);
	}

	public function cart()
	{
		return View::make('products::products.cart', $this->data);
	}

	public function cart_add()
	{
		$Product = App::make('Product');
		$product = $Product::with('images', 'options')->findOrFail(Input::get('product_id'));

		$product->markSelectedOptions(Input::get('options'));
		$this->Cart->add($product, Input::get('qty'));

		return Redirect::back()->with('success', array(
			'This product has been added to your cart!',
			'<a href="' . url('cart') . '">View Cart</a>'
		))->withInput(); // With input so that the options drop-downs stay the same.
	}

	public function cart_qty()
	{
		foreach (Input::get('qty') as $key=>$qty) {
			$this->Cart->quantity($key, $qty);
		}

		return number_format($this->Cart->total(), 2);
	}

	public function cart_remove($key)
	{
		$this->Cart->remove(urldecode($key));

		return Redirect::to('cart');
	}

	public function checkout()
	{
		if (!$this->Cart->count()) return Redirect::to('cart');
		return View::make('products::products.checkout', $this->data);
	}

	public function charge()
	{
		if (!Input::get('stripeToken')) {
			return 'The Stripe token was not generated correctly.';
		}

		$validator = Validator::make(Input::all(), array(
			'email'            => 'required|email',
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

		if (!$this->Cart->enoughInventory()) {
			return 'inventory_fail';
		}

		Stripe::setApiKey(Config::get('products::stripe.' . $this->settings['stripe']['value'] . '.secret'));

		try {
			$charge = Stripe_Charge::create(array(
				'amount'   => ToolBelt::pennies($this->Cart->total()),
				'currency' => 'usd',
				'card'     => Input::get('stripeToken')
			));
		} catch (Stripe_CardError $e) {
			return $e->getMessage();
		}

		$this->Cart->subtractInventory();

		$Order            = App::make('Order');
		$order            = new $Order;
		$order->email     = Input::get('email');
		$order->charge_id = $charge->id;
		$order->total     = $this->Cart->total();

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
		$order->shipping_address = $charge->metadata['shipping'] = json_encode($shipping);

		if (Auth::check()) {
			$order->user_id = Auth::user()->id;
		}

		$order->cart = json_encode($this->Cart->all());
		$order->save();

		$charge->metadata['order_id'] = $order->id;
		$charge->save();

		Session::put('just-ordered', $order->id);
		$this->Cart->destroy();

		$this->data['order'] = $order;

		$this->email_receipt($order);

		return 1;
	}

	public function email_receipt($order)
	{
		$this->data['order'] = $order;
		Mail::send('products::orders.emails.receipt', $this->data, function($message) use ($order) {
			$message->from($this->data['settings']['emails']['value']);
			$message->to($order->email)->subject('Receipt for Order #' . $order->id);
		});
	}

	public function inventory_fail()
	{
		return Redirect::to('cart')->withErrors('Apologies!  Our inventory is not sufficient to satisfy this order.  Someone purchased the product(s) just before you did!  Shoot!  We have adjusted the product(s) quantities and/or removed them from your cart.  Please verify these new quantities and proceed with the checkout again if you are satisfied.  Your card has not been charged.');
	}

	public function order_summary()
	{
		if (!Session::get('just-ordered')) {
			return Redirect::to('/');
		}

		$Order = App::make('Order');
		$this->data['order'] = $Order::findOrFail(Session::get('just-ordered'));
		return View::make('products::orders.summary', $this->data);
	}

}
