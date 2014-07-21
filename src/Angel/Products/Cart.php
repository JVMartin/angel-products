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
	 * Create a unique key for the product based on its selected options or the custom options
	 * if they exist.  Also, compile the price for this unique variation.
	 *
	 * @param Product &$product - The product we're generating a key for.
	 * @param int &$price - While looping through options, we'll compile the price as well.
	 * @param array $custom_options - Optional custom options.
	 * @return string $key - The unique key.
	 */
	private function cartKey(&$product, $custom_options = array(), &$price = 0)
	{
		$key = $product->id . '|';
		$price = $product->price;

		// Some deployments use custom options and some don't.  So, instead of using
		// an Illuminate Collection, we're going to use simple standard objects by
		// JSON encoding and decoding the options collection when there are no custom options,
		// or the custom options array when it exists.
		if (count($custom_options)) {
			foreach ($custom_options as $option_name=>$custom_option) {
				$product->selected_options[] = array(
					'id'       => $option_name . ':' . $custom_option['name'],
					'name'     => $custom_option['name'],
					'price'    => $custom_option['price'],
					'image'    => $custom_option['image']
				);
			}
		}

		// Now, we can treat $product->options the same and simply loop through and
		// add each selected option to our options array and add the prices as well.
		$options = array();
		foreach ($product->selected_options as $option) {
			$options[] = $option['id'];
			$price += $option['price'];
		}

		sort($options);
		$key .= implode(',', $options);

		return $key;
	}

	/**
	 * Add a product to the cart, or increase its quantity if it's already there.
	 * Be sure to have already executed $product->markSelectedOption({product_option_item_id}) for all selected option items.
	 * If you use custom options, they follow the following format:
	 *
	 * $custom_options = array(
	 *     'Size' => array(
	 *	       'name'  => 'Large',
	 *	       'price' => 10,
	 *	       'image' => 'large-shirt.jpg'
	 *     )
	 * );
	 *
	 * @param Product $product - The product model object to add.
	 * @param int $qty - How many to add to the cart.
	 * @param array $custom_options - Optional custom options.
	 * @return string $key - The key for retrieving from the cart.
	 */
	public function add($product, $qty = 1, $custom_options = array())
	{
		$key = $this->cartKey($product, $custom_options, $price);

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
	 * Remove a product from the cart.  (Again, based on its selected options.)
	 *
	 * @param Product $product
	 * @param array $custom_options - Optional custom options.
	 * @return bool - True if succeeded, false if not.
	 */
	public function remove($product, $custom_options = array())
	{
		$key = $this->cartKey($product, $custom_options);

		return $this->removeByKey($key);
	}

	/**
	 * Remove a product from the cart by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @return bool - True if succeeded, false if not.
	 */
	public function removeByKey($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		unset($this->cart[$key]);
		$this->save();

		return true;
	}

	/**
	 * Retrieve a product from the cart.
	 *
	 * @param Product $product - The desired product.
	 * @param array $custom_options - Optional custom options.
	 * @return array - The product's cart array with 'product', 'price', and 'qty'.
	 */
	public function get($product, $custom_options = array())
	{
		$key = $this->cartKey($product, $custom_options);

		return $this->getByKey($key);
	}

	/**
	 * Retrieve a product from the cart by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @return array - The product's cart array with 'product', 'price', and 'qty', or false if it doesn't exist.
	 */
	public function getByKey($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		return $this->cart[$key];
	}

	/**
	 * Adjust the cart quantity for a product variation.
	 *
	 * @param Product $product - The product to adjust.
	 * @param int $quantity - The new quantity.
	 * @param array $custom_options - Optional custom options.
	 * @return bool - Success true or false.
	 */
	public function quantity($product, $quantity, $custom_options = array())
	{
		$key = $this->cartKey($product, $custom_options);

		return $this->quantityByKey($key, $quantity);
	}

	/**
	 * Adjust the cart quantity for a product by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @param int $quantity - The new quantity.
	 * @return bool - Success true or false.
	 */
	public function quantityByKey($key, $quantity)
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
			$total += $this->totalByKey($key);
		}

		return $total;
	}

	/**
	 * Get the total dollar amount for a specific cart product by key.
	 *
	 * @return float $total - The total dollar amount for the cart product, or false if it doesn't exist.
	 */
	public function totalByKey($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		return $this->cart[$key]['price'] * $this->cart[$key]['qty'];
	}

}
