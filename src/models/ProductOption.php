<?php namespace Angel\Products;

use App;

class ProductOption extends \Eloquent {

	protected $table = 'products_options';
	public $timestamps = false;

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function items()
	{
		return $this->hasMany(App::make('ProductOptionItem'))->orderBy('order');
	}

	public function drop_down()
	{
		$arr = array();

		foreach ($this->items as $item) {
			$arr[$item->id] = $item->name;
		}

		return $arr;
	}

}