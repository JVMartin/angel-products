<?php namespace Angel\Products;

use Input, App, Redirect;

class AdminOrderController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Order';
	protected $uri		= 'orders';
	protected $plural	= 'orders';
	protected $singular	= 'order';
	protected $package	= 'products';

}