<?php namespace Angel\Products;

use Eloquent;

class ProductImage extends Eloquent {
	protected $table = 'products_images';
	public $timestamps = false;

	public function src()
	{
		return asset($this->image);
	}
	public function thumb_src()
	{
		return asset($this->thumb);
	}
}