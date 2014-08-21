<?php namespace Angel\Products;

use Illuminate\Database\Eloquent\Collection;
use Session, App;

class Cart {

	protected $cart;

	function __construct()
	{
		$this->init();
	}

	/**
	 * Retrieve the cart from the session or create it.
	 */
	protected function init()
	{
		if (!Session::has('cart')) Session::put('cart', array());

		$this->cart = Session::get('cart');
	}

	/**
	 * Save the cart back into the session.
	 */
	protected function save()
	{
		Session::put('cart', $this->cart);
	}

	/**
	 * Load a cart array in.  This is so we can use the cart getOptions(), etc.
	 * for this class from order summaries and whatnot after the card has been charged
	 * and the cart has been destroy()ed from the session.
	 *
	 * @param array $cart - The cart to load.
	 */
	public function load($cart)
	{
		$this->cart = $cart;
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
	 * @return string $key - The key for retrieving from the cart.
	 */
	public function add($product, $qty = 1)
	{
		$key = $this->key($product);

		$max_qty    = $product->qty;
		$price      = $product->price;
		$fake_price = $product->fake_price;
		foreach ($product->selected_options as $option) {
			$price += $option['price'];
			if ($fake_price > 0) $fake_price += $option['price'];
			if (isset($option['qty']) && $option['qty']) {
				$max_qty = $option['qty'];
			}
		}

		if (array_key_exists($key, $this->cart)) {
			$desired_qty = $this->cart[$key]['qty'] + $qty;
			if (isset($this->cart[$key]['max_qty'])) {
				$this->cart[$key]['qty'] = ($desired_qty > $this->cart[$key]['max_qty']) ? $this->cart[$key]['max_qty'] : $desired_qty;
			}
		} else {
			$this->cart[$key] = array(
				'product'    => $product->toJson(),
				'price'      => $price,
				'fake_price' => $fake_price,
				'qty'        => $qty
			);
			if ($product->inventory) {
				$this->cart[$key]['max_qty'] = $max_qty;
				$this->cart[$key]['qty'] = ($qty > $max_qty) ? $max_qty : $qty;
			}
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
	 * Return the cart array.
	 *
	 * @return array - The cart.
	 */
	public function all()
	{
		return $this->cart;
	}

	/**
	 * Count the items in the cart.
	 * @return int $count
	 */
	public function count()
	{
		$count = 0;
		foreach ($this->cart as $item) {
			$count += $item['qty'];
		}
		return $count;
	}

	/**
	 * Retrieve an array of selected options on the item, sorted by order.
	 *
	 * @param string $key - The unique key, returned from add().
	 */
	public function getOptions($key)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		$product = json_decode($this->cart[$key]['product']);

		$options = array();
		foreach ($product->selected_options as $string=>$option) {
			$pieces     = explode(':', $string);
			$group_name = $pieces[0];
			$options[$group_name] = $option;
		}

		uasort($options, function($a, $b) {
			return ($a->order > $b->order);
		});

		return $options;
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

		if ($quantity == 0) return $this->remove($key);

		if (isset($this->cart[$key]['max_qty']) && $quantity > $this->cart[$key]['max_qty']) {
			$quantity = $this->cart[$key]['max_qty'];
		}
		$this->cart[$key]['qty'] = $quantity;
		$this->save();

		return true;
	}

	/**
	 * Adjust the cart maximum quantity for a product by its unique key.
	 *
	 * @param string $key - The unique key, returned from add().
	 * @param int $quantity - The new quantity.
	 * @return bool - Success true or false.
	 */
	public function maxQuantity($key, $max_quantity)
	{
		if (!array_key_exists($key, $this->cart)) return false;

		if ($max_quantity == 0) return $this->remove($key);

		$this->cart[$key]['max_qty'] = $max_quantity;
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


	/**
	 * Get all the cart products and cache them.
	 */
	protected $products = null;
	public function products()
	{
		if ($this->products) return $this->products;

		$Product = App::make('Product');
		$product_ids = array();
		foreach ($this->decoded() as $item) {
			$product_ids[] = $item['product']['id'];
		}
		if (!count($product_ids)) return new Collection;
		$this->products = $Product::whereIn('id', $product_ids)->get();
		return $this->products;
	}


	/**
	 * Get all the selected option items and cache them.
	 */
	protected $optionItems = null;
	public function optionItems()
	{
		if ($this->optionItems) return $this->optionItems;

		$ProductOptionItem = App::make('ProductOptionItem');

		$item_ids = array();
		foreach ($this->decoded() as $item) {
			if (!count($item['product']['selected_options'])) continue;
			foreach ($item['product']['selected_options'] as $selected_option) {
				if (!isset($selected_option['id'])) continue;
				$item_ids[] = $selected_option['id'];
			}
		}
		if (!count($item_ids)) return new Collection;
		$this->optionItems = $ProductOptionItem::whereIn('id', $item_ids)->get();
		return $this->optionItems;
	}

	/**
	 * JSON Decode all the cart products and cache them.
	 */
	protected $decoded = null;
	public function decoded()
	{
		if ($this->decoded) return $this->decoded;

		$this->decoded = $this->cart;
		foreach ($this->decoded as &$item) {
			$item['product'] = json_decode($item['product'], true);
		}

		return $this->decoded;
	}

	public function enoughInventory()
	{
		$enough = true;

		foreach ($this->decoded() as $key=>$item) {
			if (!isset($item['max_qty'])) continue;

			$product = $this->products()->find($item['product']['id']);
			if (!$product) {
				// Product no longer exists
				$enough = false;
				$this->remove($key);
				continue;
			}
			$selected_option = null;
			if (count($item['product']['selected_options'])) {
				$selected_option = array_shift($item['product']['selected_options']);
			}
			if ($selected_option) {
				$optionItem = $this->optionItems()->find($selected_option['id']);
				if (!$optionItem) {
					// Option no longer exists
					$enough = false;
					$this->remove($key);
					continue;
				}
				if ($optionItem->qty < $item['qty']) {
					// Not enough products of that selected option
					$enough = false;
					$this->quantity($key, $optionItem->qty);
					$this->maxQuantity($key, $optionItem->qty);
					continue;
				}
			} else {
				if ($product->qty < $item['qty']) {
					// Not enough of the product
					$enough = false;
					$this->quantity($key, $product->qty);
					$this->maxQuantity($key, $optionItem->qty);
					continue;
				}
			}
		}

		return $enough;
	}

}
