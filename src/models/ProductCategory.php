<?php namespace Angel\Products;

use View, App;

class ProductCategory extends \Angel\Core\LinkableModel {

	protected $table = 'products_categories';

	protected $slugSeed    = 'name';
	protected $reorderable = true;

	public static function columns()
	{
		return array(
			'name',
			'image'
		);
	}

	public function validate_rules()
	{
		return array(
			'name' => 'required'
		);
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function children()
	{
		return $this->hasMany(get_class($this), 'parent_id')->orderBy('order');
	}
	public function products()
	{
		return $this->belongsToMany(App::make('Product'), 'products_categories_products');
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

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		return url('products/categories/' . $this->slug);
	}
	public function link_edit()
	{
		return admin_url('products/categories/edit/' . $this->id);
	}
	public function search($terms)
	{
		return static::where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('name', 'like', $term);
			}
		})->get();
	}

}