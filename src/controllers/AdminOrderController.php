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
		$this->data['order'] = $Order::findOrFail($id);
		return View::make($this->view('show'), $this->data);
	}

}