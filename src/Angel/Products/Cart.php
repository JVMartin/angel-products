<?php namespace Angel\Products;

use Session;

class Cart {

	private $cart;

	function __construct()
	{
		$this->get();
	}

	private function get()
	{
		if (!Session::has('cart')) Session::put('cart', array());

		$this->cart = Session::get('cart');
	}

	private function save()
	{
		Session::put('cart', $this->cart);
	}

	public function destroy()
	{
		$this->cart = array();
		$this->save();
	}

	/*
	$custom_options = array(
		'Size' => array(
			'name'  => 'Large',
			'price' => 10,
			'image' => 'large-shirt.jpg'
		)
	);
	*/
	private function cartKey(&$product, &$price = 0, $custom_options = array())
	{
		$key = $product->id . '|';
		$price = $product->price;

		if (count($custom_options)) {
			$temp_options = array();
			foreach ($custom_options as $id=>$custom_option) {
				$temp_options[] = array(
					'id' => $id,
					'name' => $id,
					'items' => array(
						array(
							'id'       => $id,
							'name'     => $custom_option['name'],
							'price'    => $custom_option['price'],
							'image'    => $custom_option['image'],
							'selected' => true
						)
					)
				);
			}
			$product->options = json_decode(json_encode($temp_options));
		} else {
			$product->options = json_decode($product->options->toJson());
		}

		$options = array();
		foreach ($product->options as $option) {
			foreach ($option->items as $item) {
				if (!property_exists($item, 'selected') || !$item->selected) continue;
				$options[] = $item->id;
				$price += $item->price;
			}
		}

		sort($options);
		$key .= implode(',', $options);

		return $key;
	}

	public function add($product, $qty = 1, $custom_options = array())
	{
		$key = $this->cartKey($product, $price, $custom_options);

		if (array_key_exists($key, $this->cart)) {
			$this->cart[$key]['qty'] += $qty;
		} else {
			$this->cart[$key] = array(
				'product' => $product->toJson(),
				'price'   => $price,
				'qty'     => $qty
			);
		}

		$this->save();

		return true;
	}

	public function remove($product)
	{
		$key = $this->cartKey($product);

		if (!array_key_exists($key, $this->cart)) return false;

		unset($this->cart[$key]);
		$this->save();

		return true;
	}

}
