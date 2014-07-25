<?php namespace Angel\Products;

use App, View, Input, Redirect;

class AdminProductCategoryController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';

	protected $slug     = 'name';

	public function index($id = null)
	{
		$ProductCategory = App::make('ProductCategory');

		$temp_categories = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();

		$categories = $ProductCategory::tree($temp_categories);

		$this->data['categories'] = $categories;

		return View::make($this->view('index'), $this->data);
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
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
			$parent_id = ($parent_id == 'null') ? null : $parent_id;
			$category = $categories->find($category_id);
			$category->parent_id = $parent_id;
			$category->order = $order;
			$category->save();
			$order++;
		}

		return Redirect::to(admin_uri('products/categories'))->with('success', 'Category tree saved.');
	}

	public function show_products($id)
	{
		$ProductCategory = App::make('ProductCategory');
		$Product = App::make('Product');

		$categories = $ProductCategory::orderBy('parent_id')->orderBy('order')->get();

		$paginator = $Product::with('images')->where('category_id', $id)->paginate();

		$this->data['crumbs'] = $ProductCategory::crumbs($categories, $id);
		$this->data['category'] = $categories->find($id);
		$this->data['products'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
		return View::make($this->view('show-products'), $this->data);
	}

}