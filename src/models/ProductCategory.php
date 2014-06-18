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

	/**
	 * Create the HTML for breadcrumbs for a category.
	 *
	 * @param Collection $categories - A collection of all categories
	 * @param int $category_id - The current visible category
	 * @param string $url - The URL to use for the crumbs, with {id} signifying where the category ID should go for each crumb
	 * @return HTML of the breadcrumbs
	 */
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

	/**
	 * Creates a nested array of categories and their children.
	 *
	 * @param $categories - An Illuniate Collection of all product categories.
	 * @param null $parent_id - Used for recursively creating branches.
	 * @return array - The completed branch.
	 */
	public static function tree($categories, $parent_id = null)
	{
		$branch = array();

		foreach ($categories as $category) {
			if ($category->parent_id != $parent_id) continue;

			$children = ProductCategory::tree($categories, $category->id);

			if (count($children)) {
				$category->children = $children;
			}

			$branch[] = $category;
		}

		return $branch;
	}

}