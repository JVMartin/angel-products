<?php namespace Angel\Products;

use Eloquent;

class ProductOption extends Eloquent {

	protected $table = 'products_options';
	public $timestamps = false;

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function items()
	{
		return $this->hasMany(App::make('ProductOptionItem'));
	}

}