<?php namespace Angel\Products;

use Angel\Core\LinkableModel;
use Config, App;

class Product extends LinkableModel {

	public $selected_options = array();

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function category()
	{
		return $this->belongsTo(App::make('ProductCategory'));
	}
	public function options()
	{
		return $this->hasMany(App::make('ProductOption'))->with('items')->orderBy('order');
	}
	public function images()
	{
		return $this->hasMany(App::make('ProductImage'))->orderBy('order');
	}
	public function related()
	{
		return $this->belongsToMany(App::make('Product'), 'products_related_products', 'related_id')->orderBy('products_related_products.order');
	}
	public function changes()
	{
		$Change = App::make('Change');

		return $Change::where('fmodel', 'Product')
			->where('fid', $this->id)
			->with('user')
			->orderBy('created_at', 'DESC')
			->get();
	}

	/**
	 * Mark an option_item as selected by that option_item's ID.
	 *
	 * @param int $option_item_id
	 */
	public function markSelectedOption($option_item_id, $option_order = 0)
	{
		$this->options->each(function($option) use ($option_item_id, $option_order) {
			$option->items->each(function($option_item) use ($option, $option_item_id, $option_order) {
				$arr = $option_item->toArray();
				$arr['order'] = $option_order;
				if ($option_item->id == $option_item_id) $this->selected_options[$option->name . ':' . $option_item->name] = $arr;
			});
		});
		ksort($this->selected_options);
	}

	/**
	 * Same as markSelectedOption() but takes an array.
	 *
	 * @param array $option_item_id
	 */
	public function markSelectedOptions($options)
	{
		if (!is_array($options)) return false;

		foreach ($options as $option_id=>$option_item_id) {
			$option = $this->options->find($option_id);
			$this->markSelectedOption($option_item_id, $option->order);
		}
	}

	/**
	 * Add custom option(s) to the product in this format:
	 *
	 * $options = array(
	 *     'Size' => array(
	 *	       'name'  => 'Large',
	 *	       'price' => 10,
	 *	       'image' => 'large-shirt.jpg'
	 *     )
	 * );
	 *
	 * @param $options - Array of options
	 */
	public function addCustomOptions($options)
	{
		foreach ($options as $name=>$option) {
			$this->selected_options[$name . ':' . $option['name']] = array(
				'name'     => $option['name'],
				'price'    => isset($option['price']) ? $option['price'] : 0,
				'image'    => isset($option['image']) ? $option['image'] : null
			);
		}
		ksort($this->selected_options);
	}

	/**
	 * We need to override toArray() to make it include the special selected_options array.
	 */
	public function toArray()
	{
		$array = parent::toArray();
		$array['selected_options'] = $this->selected_options;
		return $array;
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		$language_segment = (Config::get('core::languages')) ? $this->language->uri . '/' : '';

		return url($language_segment . 'products/' . $this->slug);
	}
	public function link_edit()
	{
		return admin_url('products/edit/' . $this->id);
	}

}