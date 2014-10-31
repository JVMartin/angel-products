<?php namespace Angel\Products;

use App, View, Input, Redirect;

class AdminProductCategoryController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';

	public function index()
	{
		$ProductCategory = App::make('ProductCategory');
		$categories      = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();
		$this->data['categories'] = $ProductCategory::tree($categories);

		return View::make($this->view('index'), $this->data);
	}

	public function update_tree()
	{
		$ProductCategory = App::make('ProductCategory');

		parse_str(Input::get('tree'), $tree);

		if (!isset($tree['category'])) {
			return Redirect::to(admin_uri('products/categories'))->with('success', 'Category tree saved.');
		}

		$categories = $ProductCategory::all();
		$order = 0;
		foreach ($tree['category'] as $category_id=>$parent_id) {
			$parent_id           = ($parent_id == 'null') ? null : $parent_id;
			$category            = $categories->find($category_id);
			$category->parent_id = $parent_id;
			$category->order     = $order;
			$category->save();
			$order++;
		}

		return Redirect::to(admin_uri('products/categories'))->with('success', 'Category tree saved.');
	}

	public function show_products($id)
	{
		$ProductCategory = App::make('ProductCategory');

		$categories = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();
		$category   = $categories->find($id);
		$paginator  = $category->products()->with('images')->paginate();

		$this->data['crumbs']   = $ProductCategory::crumbs($categories, $id);
		$this->data['category'] = $category;
		$this->data['products'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links']    = $paginator->appends($appends)->links();
		return View::make($this->view('show-products'), $this->data);
	}

}