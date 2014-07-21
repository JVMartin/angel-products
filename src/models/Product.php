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

	public function markSelectedOption($option_item_id)
	{
		$this->options->each(function($option) use ($option_item_id) {
			$option->items->each(function($option_item) use ($option_item_id) {
				if ($option_item->id == $option_item_id) $this->selected_options[] = $option_item->toArray();
			});
		});
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