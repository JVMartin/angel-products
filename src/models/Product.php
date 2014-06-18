<?php

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

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function category()
	{
		return $this->belongsTo('ProductCategory');
	}
	public function options()
	{
		return $this->hasMany('ProductOption');
	}
	public function images()
	{
		return $this->hasMany('ProductImage')->orderBy('order');
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		$language_segment = (Config::get('core::languages')) ? $this->language->uri . '/' : '';

		return url($language_segment . 'products/' . $this->id);
	}
	public function link_edit()
	{
		return admin_url('products/edit/' . $this->id);
	}

}