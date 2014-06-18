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

	public function children()
	{
		return $this->hasMany('ProductCategory', 'parent_id');
	}

	public static function crumbs($categories, $category_id, $url = null)
	{
		$url = (!$url) ? admin_url('products/categories/expand/{id}') : $url;
		$crumbs = array();

		do {
			$category = $categories->find($category_id);
			$crumbs[$category_id] = $category->name;
			$category_id = $category->parent_id;
		} while($category_id);

		$crumbs = array_combine(array_reverse(array_keys($crumbs)), array_reverse($crumbs));

		return View::make('products::products.categories.crumbs', compact('crumbs', 'url'));
	}

}