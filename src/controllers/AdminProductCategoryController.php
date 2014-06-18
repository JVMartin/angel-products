<?php

class AdminProductCategoryController extends AdminCrudController {

	protected $model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';

	private function categories_tree($categories, $parent_id = null)
	{
		$branch = array();

		foreach ($categories as $category) {
			if ($category->parent_id != $parent_id) continue;

			$children = $this->categories_tree($categories, $category->id);

			if (count($children)) {
				$category->children = $children;
			}

			$branch[] = $category;
		}

		return $branch;
	}

	public function index()
	{
		$temp_categories = ProductCategory::orderBy('parent_id')->orderBy('order')->get();

		$categories = $this->categories_tree($temp_categories);

		$this->data['categories'] = $categories;

		return View::make($this->view('index'), $this->data);
	}

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	public function update_tree()
	{
		parse_str(Input::get('tree'), $tree);
		if (!isset($tree['category'])) {
			return Redirect::to(admin_uri('products/categories'))->with('success', 'No changes were made.');
		}
		$categories = ProductCategory::all();
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

}