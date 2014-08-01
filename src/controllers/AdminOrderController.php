<?php namespace Angel\Products;

use App, View, Input, Redirect;

class AdminOrderController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Order';
	protected $uri		= 'orders';
	protected $plural	= 'orders';
	protected $singular	= 'order';
	protected $package	= 'products';

	public function index()
	{
		$Model = App::make($this->Model);
		$objects = $Model::orderBy('id', 'desc');

		$paginator = $objects->paginate();
		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make($this->view('index'), $this->data);
	}

	public function show($id)
	{
		$Order = App::make('Order');
		$this->data['order'] = $Order::findOrFail($id);
		return View::make($this->view('show'), $this->data);
	}

	public function mark_shipped($id)
	{
		$Order = App::make('Order');
		$order = $Order::findOrFail($id);
		$order->shipped = Input::has('mark-shipped');
		$order->save();
		return Redirect::to(admin_uri('orders/show/' . $order->id));
	}

}