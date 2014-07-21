<?php namespace Angel\Products;

use Session;

class Cart {

	private $cart;

	function __construct()
	{
		$this->init();
	}

	/**
	 * Retrieve the cart from the session or create it.
	 */
	private function init()
	{
		if (!Session::has('cart')) Session::put('cart', array());

		$this->cart = Session::get('cart');
	}

	/**
	 * Save the cart back into the session.
	 */
	private function save()
	{
		Session::put('cart', $this->cart);
	}

	/**
	 * Empty the cart, removing all items.
	 */
	public function destroy()
	{
		$this->cart = array();
		$this->save();
	}

	/**
	 * Create a unique key for the product based on its selected options.
	 *
	 * @param Product &$product - The product we're generating a key for.
	 * @return string $key - The unique key.
	 */
	public function key($product)
	{
		return $product->id . '|' . implode(',', array_keys($product->selected_options));
	}

	/**
	 * Add a product to the cart, or increase its quantity if it's already there.
	 * Be sure to have already executed $product->markSelectedOption({product_option_item_id})
	 * or $product->addCustomOptions({options_array}) before adding the product.
	 *
	 * @param Product $product - The product model object to add.
	 * @param int $qty - How many to add to the cart.
	 * @param array $custom_options - Optional custom options.
	 * @return string $key - The key for retrieving from the cart.
	 */
	public function add($product, $qty = 1)
	{
		$key = $this->key($product);

		$price = $product->price;
		foreach ($product->selected_options as $option) {
			$price += $option['price'];
		}

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

		return $key;
	}

	/**
	 * Remove a product from the cart by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @return bool - True if succeeded, false if not.
	 */
	public function remove($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		unset($this->cart[$key]);
		$this->save();

		return true;
	}

	/**
	 * Retrieve a product from the cart by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @return array - The product's cart array with 'product', 'price', and 'qty', or false if it doesn't exist.
	 */
	public function get($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		return $this->cart[$key];
	}

	/**
	 * Adjust the cart quantity for a product by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @param int $quantity - The new quantity.
	 * @return bool - Success true or false.
	 */
	public function quantity($key, $quantity)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		$this->cart[$key]['qty'] = $quantity;
		$this->save();

		return true;
	}

	/**
	 * Get the total dollar amount for the cart's contents.
	 *
	 * @return float $total - The total dollar amount.
	 */
	public function total()
	{
		$total = 0;

		foreach (array_keys($this->cart) as $key) {
			$total += $this->totalForKey($key);
		}

		return $total;
	}

	/**
	 * Get the total dollar amount for a specific cart product variation.
	 *
	 * @return float $total - The total dollar amount for the cart product, or false if it doesn't exist.
	 */
	public function totalForKey($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		return $this->cart[$key]['price'] * $this->cart[$key]['qty'];
	}

}
