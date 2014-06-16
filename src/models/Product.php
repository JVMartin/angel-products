<?php

class Product extends Eloquent {

	// Columns to update/insert on edit/add
	public static function columns()
	{
		return array(
			'name',
			'size',
			'description',
			'category_id',
			'subcategory_id',
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
	public function subcategory()
	{
		return $this->belongsTo('ProductSubcategory');
	}
	public function options()
	{
		return $this->hasMany('ProductOption');
	}
	public function images()
	{
		return $this->hasMany('ProductImage');
	}

}

?>