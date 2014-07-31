<?php namespace Angel\Products;

use App, View;

class AdminOrderController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Order';
	protected $uri		= 'orders';
	protected $plural	= 'orders';
	protected $singular	= 'order';
	protected $package	= 'products';

	public function show($id)
	{
		$Order = App::make('Order');

		$order = $Order::findOrFail($id);

		$this->data['order']            = $order;
		$this->data['cart']             = json_decode($order->cart);
		$this->data['billing_address']  = json_decode($order->billing_address);
		$this->data['shipping_address'] = json_decode($order->shipping_address);
		$this->data['shipping_address'] = json_decode($order->shipping_address);

		return View::make($this->view('show'), $this->data);
	}

}