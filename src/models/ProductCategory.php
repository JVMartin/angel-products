<?php

class ProductCategory extends Eloquent {

	protected $table = 'products_categories';

	// Columns to update/insert on edit/add
	public static function columns()
	{
		return array(
			'name',
			'image'
		);
	}

}