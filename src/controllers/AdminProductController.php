<?php namespace Angel\Products;

use Illuminate\Database\Eloquent\Collection;
use Input, App, Redirect;

class AdminProductController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Product';
	protected $uri		= 'products';
	protected $plural	= 'products';
	protected $singular	= 'product';
	protected $package	= 'products';

	protected $log_changes = true;
	protected $slug        = 'name';

	// Columns to update on edit/add
	protected static function columns()
	{
		return array(
			'name',
			'size',
			'html',
			'price',
			'fake_price',
			'new',
			'inventory',
			'qty'
		);
	}

	public function add()
	{
		$ProductCategory = App::make('ProductCategory');

		$this->data['ProductCategory'] = $ProductCategory;
		$this->data['Product']         = App::make('Product');
		$this->data['categories']      = $ProductCategory::with('products')->orderBy('order')->get();
		return parent::add();
	}

	public function add_redirect($product)
	{
		return Redirect::to(admin_uri('products/categories/show-products/' . $product->categories->first()->id))->with('success', '
			<p>Product successfully created.</p>
		');
	}

	public function edit($id)
	{
		$ProductCategory = App::make('ProductCategory');

		$this->data['ProductCategory'] = $ProductCategory;
		$this->data['Product']         = App::make('Product');
		$this->data['categories']      = $ProductCategory::with('products')->orderBy('order')->get();
		return parent::edit($id);
	}

	public function edit_redirect($product)
	{
		return Redirect::to($this->uri('edit/' . $product->id))->with('success', '
			<p>Product successfully updated.</p>
			<p><a href="' . admin_url('products/categories/show-products/' . $product->categories->first()->id) . '">Return to index</a></p>
		');
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	public function before_save(&$product, &$changes = array())
	{
		$product->plaintext = strip_tags($product->html);
	}

	/**
	 * Handle images and options.
	 */
	public function after_save($product, &$changes = array())
	{
		$this->handle_categories($product, $changes);
		$this->handle_images($product, $changes);
		$this->handle_options($product, $changes);
		$this->handle_related($product, $changes);
	}

	protected function handle_categories($product, &$changes)
	{
		$ids = count(Input::get('categories')) ? Input::get('categories') : array();
		$category_changes = $product->categories()->sync($ids);
		foreach ($category_changes['attached'] as $id) {
			$changes['Added product to Category ID#' . $id] = array();
		}
		foreach ($category_changes['detached'] as $id) {
			$changes['Removed product from Category ID#' . $id] = array();
		}
	}

	protected function handle_related($product, &$changes)
	{
		$ids = count(Input::get('related')) ? Input::get('related') : array();
		$related_changes = $product->related()->sync($ids);
		foreach ($related_changes['attached'] as $id) {
			$changes['Added related Product ID#' . $id] = array();
		}
		foreach ($related_changes['detached'] as $id) {
			$changes['Removed related Product ID#' . $id] = array();
		}
	}

	protected function handle_images($product, &$changes)
	{
		$ProductImage = App::make('ProductImage');

		// Get all existing images for the product
		$images       = $ProductImage::where('product_id', $product->id)->get();
		$input_ids    = Input::get('imageIDs');
		$input_images = Input::get('images');
		$input_thumbs = Input::get('imageThumbs');
		foreach ($input_ids as $order=>$image_id) {
			$input_image = $input_images[$order];
			$input_thumb = $input_thumbs[$order];

			// Grab the existing image from the collection if it exists
			$image     = $images->find($image_id);
			$old_array = ($image) ? $image->toArray() : array();

			// If there's no existing image and the input is empty, don't create a new one
			if (!$image && !$input_image) continue;

			// Update image or create new one
			$image = ($image) ? $image : new $ProductImage;
			$image->product_id	= $product->id;
			$image->order		= $order;
			$image->image		= $input_image;
			$image->thumb		= $input_thumb;
			$image->save();

			$this->log_relation_change($image, $old_array, array('order', 'image', 'thumb'), $changes);
		}

		// Delete all images not in input
		foreach ($images as $image) {
			if (!in_array($image->id, $input_ids)) {
				$this->log_relation_deletion($image, $changes);
				$image->delete();
			}
		}
	}

	protected function handle_options($product, &$changes)
	{
		$ProductOption     = App::make('ProductOption');
		$ProductOptionItem = App::make('ProductOptionItem');

		// Get all existing options and option items
		$options    = $ProductOption::where('product_id', $product->id)->get();
		$option_ids = array();
		foreach ($options as $option) {
			$option_ids[] = $option->id;
		}
		$items = (count($option_ids)) ? $ProductOptionItem::whereIn('product_option_id', $option_ids)->get() : new Collection;

		$input_options    = Input::get('options');
		$input_option_ids = array();
		$input_item_ids   = array();
		foreach ($input_options as $order=>$input_option) {
			if ($input_option['id']) $input_option_ids[] = $input_option['id'];

			// Don't create new options when there is no name
			if (!$input_option['id'] && (!isset($input_option['name']) || !$input_option['name'])) continue;

			$option    = $options->find($input_option['id']);
			$old_array = ($option) ? $option->toArray() : array();

			// Update option or create new one
			$option = ($option) ? $option : new $ProductOption;
			$option->product_id = $product->id;
			$option->order      = $order;
			$option->name       = $input_option['name'];
			$option->save();

			$this->log_relation_change($option, $old_array, array('order', 'name'), $changes);

			foreach ($input_option['items'] as $order=>$input_item) {
				if ($input_item['id']) $input_item_ids[] = $input_item['id'];

				// Don't create new option items when there is no name
				if (!$input_item['id'] && (!isset($input_item['name']) || !$input_item['name'])) continue;

				$item      = $items->find($input_item['id']);
				$old_array = ($item) ? $item->toArray() : array();

				// Update option item or create new one
				$item = ($item) ? $item : new $ProductOptionItem;
				$item->product_option_id = $option->id;
				$item->order             = $order;
				$item->name              = $input_item['name'];
				$item->price             = $input_item['price'];
				$item->qty               = $input_item['qty'];
				$item->image             = $input_item['image'];
				$item->save();

				$this->log_relation_change($item, $old_array, array('order', 'name', 'price', 'qty', 'image'), $changes);
			}
		}

		// Delete all options not in input
		foreach ($options as $option) {
			if (!in_array($option->id, $input_option_ids)) {
				$this->log_relation_deletion($option, $changes);
				$option->delete();
			}
		}

		// Delete all option items not in input
		foreach ($items as $item) {
			if (!in_array($item->id, $input_item_ids)) {
				$this->log_relation_deletion($item, $changes);
				$item->delete();
			}
		}
	}

}
