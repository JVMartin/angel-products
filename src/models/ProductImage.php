<?php namespace Angel\Products;

use Eloquent;

class ProductImage extends Eloquent {
	protected $table = 'products_images';
	public $timestamps = false;
}