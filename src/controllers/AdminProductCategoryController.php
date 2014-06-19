<?php namespace Angel\Products;

use Angel\Core\AdminCrudController;
use App, View, Input, Redirect;

class AdminProductCategoryController extends AdminCrudController {

	protected $model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';

	public function index($id = null)
	{
		$productCategoryModel = App::make('ProductCategory');

		$temp_categories = $productCategoryModel::orderBy('parent_id')->orderBy('order')->get();

		$categories = $productCategoryModel::tree($temp_categories);

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
		$productCategoryModel = App::make('ProductCategory');

		parse_str(Input::get('tree'), $tree);

		if (!isset($tree['category'])) {
			return Redirect::to(admin_uri('products/categories'))->with('success', 'Category tree saved.');
		}

		$categories = $productCategoryModel::all();
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

	public function expand($id)
	{
		$productCategoryModel = App::make('ProductCategory');
		$productModel = App::make('Product');

		$categories = $productCategoryModel::orderBy('parent_id')->orderBy('order')->get();

		$paginator = $productModel::with('images')->where('category_id', $id)->paginate();

		$this->data['crumbs'] = $productCategoryModel::crumbs($categories, $id);
		$this->data['category'] = $categories->find($id);
		$this->data['products'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
		return View::make($this->view('expand'), $this->data);
	}

}