<?php namespace Angel\Products;

use Illuminate\Database\Eloquent\Collection;
use Input, App, Redirect;

class AdminProductController extends \Angel\Core\AdminCrudController {

	protected $Model	= 'Product';
	protected $uri		= 'products';
	protected $plural	= 'products';
	protected $singular	= 'product';
	protected $package	= 'products';

	protected $slug     = 'name';

	public function add()
	{
		$ProductCategory = App::make('ProductCategory');

		$this->data['categories'] = $ProductCategory::orderBy('order')->get();
		return parent::add();
	}

	public function add_redirect($object)
	{
		return Redirect::to(admin_uri('products/categories/show-products/' . $object->category_id))->with('success', '
			<p>Product successfully created.</p>
		');
	}

	public function edit($id)
	{
		$ProductCategory = App::make('ProductCategory');

		$this->data['categories'] = $ProductCategory::orderBy('order')->get();
		return parent::edit($id);
	}

	public function edit_redirect($object)
	{
		return Redirect::to($this->uri('edit/' . $object->id))->with('success', '
			<p>Product successfully updated.</p>
			<p><a href="' . admin_url('products/categories/show-products/' . $object->category_id) . '">Return to index</a></p>
		');
	}

	public function validate_rules($id = null)
	{
		return array(
			'category_id' => 'required',
			'name'        => 'required'
		);
	}

	public function after_save($product, &$changes = array())
	{
		$ProductImage      = App::make('ProductImage');
		$ProductOption     = App::make('ProductOption');
		$ProductOptionItem = App::make('ProductOptionItem');

		$images       = $ProductImage::where('product_id', $product->id)->get();
		$input_ids    = Input::get('imageIDs');
		$input_images = Input::get('images');
		$input_thumbs = Input::get('imageThumbs');
		foreach ($input_ids as $order=>$image_id) {
			$input_image = $input_images[$order];
			$input_thumb = $input_thumbs[$order];

			$image = $images->find($image_id);
			// Skip empty images that don't exist
			if (!$image && !$input_image) continue;

			$image = ($image) ? $image : new $ProductImage;
			$image->product_id	= $product->id;
			$image->image		= $input_image;
			$image->order		= $order;
			$image->thumb		= $input_thumb;
			$image->save();
		}

		// Delete deleted images
		foreach ($images as $image) {
			if (!in_array($image->id, $input_ids)) {
				$image->delete();
			}
		}

		$options    = $ProductOption::where('product_id', $product->id)->get();
		$option_ids = array();
		foreach ($options as $option) {
			$option_ids[] = $option->id;
		}
		$items = (count($option_ids)) ? $ProductOptionItem::whereIn('product_option_id', $option_ids)->get() : new Collection;

		$input_options = Input::get('options');
		$input_option_ids = array();
		$input_item_ids   = array();
		foreach ($input_options as $order=>$input_option) {
			$input_option_ids[] = $input_option['id'];

			if (!isset($input_option['name']) || !$input_option['name']) continue;

			$option = $options->find($input_option['id']);

			$option = ($option) ? $option : new $ProductOption;
			$option->product_id = $product->id;
			$option->order      = $order;
			$option->name       = $input_option['name'];
			$option->save();

			foreach ($input_option['items'] as $order=>$input_item) {
				$input_item_ids[] = $input_item['id'];

				if (!isset($input_item['name']) || !$input_item['name']) continue;

				$item = $items->find($input_item['id']);

				$item = ($item) ? $item : new $ProductOptionItem;
				$item->product_option_id = $option->id;
				$item->order             = $order;
				$item->name              = $input_item['name'];
				$item->price             = $input_item['price'];
				$item->image             = $input_item['image'];
				$item->save();
			}
		}

		foreach ($options as $option) {
			if (!in_array($option->id, $input_option_ids)) {
				$option->delete();
			}
		}

		foreach ($items as $item) {
			if (!in_array($item->id, $input_item_ids)) {
				$item->delete();
			}
		}
	}

}