<?php namespace Angel\Products;

use Angel\Core\LinkableModel;
use Config, App;

class Product extends LinkableModel {

	// Columns to update/insert on edit/add
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

	public function markSelectedOption($option_item_id)
	{
		$this->options->each(function($option) use ($option_item_id) {
			$option->items->each(function($item) use ($option_item_id) {
				if ($item->id == $option_item_id) $item->selected = true;
			});
		});
	}

	/**
	 * We need to override toArray() to make it include the special JSON mutation we do from the cart, allowing
	 * developers to pass in custom arrays of options instead of using the ProductOption relationships.
	 */
	public function toArray()
	{
		$array = parent::toArray();
		$array['options'] = $this->options;
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