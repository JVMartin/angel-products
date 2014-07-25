<?php namespace Angel\Products;

use Angel\Core\LinkableModel;
use Config, App;

class Product extends LinkableModel {

	// Columns to update/insert on edit/add from CrudController
	public static function columns()
	{
		return array(
			'name',
			'size',
			'description',
			'category_id',
			'price',
			'fake_price',
			'new'
		);
	}

	public $selected_options = array();

	/**
	 * Mark an option_item as selected by that option_item's ID.
	 *
	 * @param int $option_item_id
	 */
	public function markSelectedOption($option_item_id)
	{
		$this->options->each(function($option) use ($option_item_id) {
			$option->items->each(function($option_item) use ($option, $option_item_id) {
				if ($option_item->id == $option_item_id) $this->selected_options[$option->name . ':' . $option_item->name] = $option_item->toArray();
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

		foreach ($options as $option_item_id) {
			$this->markSelectedOption($option_item_id);
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