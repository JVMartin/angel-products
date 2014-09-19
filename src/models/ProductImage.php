<?php namespace Angel\Products;

class ProductImage extends \Eloquent {

	protected $table   = 'products_images';
	public $timestamps = false;

	public function src()
	{
		if ($this->image) return asset($this->image);
	}
	public function thumb_src()
	{
		if ($this->thumb) return asset($this->thumb);
	}

}