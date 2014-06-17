<?php

class ProductOption extends Eloquent {

	protected $table = 'products_options';
	public $timestamps = false;

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function items()
	{
		return $this->hasMany('ProductOptionItem');
	}

}