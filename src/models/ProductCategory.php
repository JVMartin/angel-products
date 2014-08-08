<?php namespace Angel\Products;

use Angel\Core\LinkableModel;
use View, Config, App;

class ProductCategory extends LinkableModel {

	protected $table = 'products_categories';

	public function children()
	{
		return $this->hasMany(get_class($this), 'parent_id')->orderBy('order');
	}
	public function products()
	{
		return $this->hasMany(App::make('Product'), 'category_id');
	}

	/**
	 * Create the HTML for breadcrumbs for a category.
	 *
	 * @param Collection $categories - A collection of all categories
	 * @param int $category_id - The current visible category
	 * @param string $url - The URL to use for the crumbs, with {id} or {slug} signifying where the category ID or slug should go for each crumb
	 * @return HTML of the breadcrumbs
	 */
	public static function crumbs($categories, $category_id, $url = null)
	{
		$url = (!$url) ? admin_url('products/categories/show-products/{id}') : $url;
		$crumbs = array();

		do {
			$category = $categories->find($category_id);
			$crumbs[$category_id] = $category;
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

			$children = static::tree($categories, $category->id);

			if (count($children)) {
				$category->children = $children;
			}

			$branch[] = $category;
		}

		return $branch;
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	// Menu link related methods - all menu-linkable models must have these
	// NOTE: Always pull models with their languages initially if you plan on using these!
	// Otherwise, you're going to be performing repeated queries.  Naughty.
	public function link()
	{
		$language_segment = (Config::get('core::languages')) ? $this->language->uri . '/' : '';

		return url($language_segment . 'products/categories/' . $this->id);
	}
	public function link_edit()
	{
		return admin_url('products/categories/edit/' . $this->id);
	}

}