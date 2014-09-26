<?php namespace Angel\Products;

use Illuminate\Database\Eloquent\Collection;
use Config, App, Input;

class Product extends \Angel\Core\LinkableModel {

	public $selected_options = array();

	protected $slugSeed = 'name';

	public static function columns()
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

	public function validate_rules()
	{
		return array(
			'name' => 'required'
		);
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($product) {
			$product->plaintext = strip_tags($product->html);
		});
		static::saved(function($product) {
			if ($product->skipEvents) return;

			$changes = array();
			$product->handle_categories($changes);
			$product->handle_images($changes);
			$product->handle_options($changes);
			$product->handle_related($changes);
			with(App::make('Change'))->log($product, $changes);
		});
	}
	protected function handle_categories(&$changes)
	{
		$ids = count(Input::get('categories')) ? Input::get('categories') : array();
		$category_changes = $this->categories()->sync($ids);
		foreach ($category_changes['attached'] as $id) {
			$changes['Added product to Category ID#' . $id] = array();
		}
		foreach ($category_changes['detached'] as $id) {
			$changes['Removed product from Category ID#' . $id] = array();
		}
	}

	protected function handle_related(&$changes)
	{
		$ids = count(Input::get('related')) ? Input::get('related') : array();
		$related_changes = $this->related()->sync($ids);
		foreach ($related_changes['attached'] as $id) {
			$changes['Added related Product ID#' . $id] = array();
		}
		foreach ($related_changes['detached'] as $id) {
			$changes['Removed related Product ID#' . $id] = array();
		}
	}

	protected function handle_images(&$changes)
	{
		$ProductImage = App::make('ProductImage');

		// Get all existing images for the product
		$images       = $ProductImage::where('product_id', $this->id)->get();
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
			$image->product_id = $this->id;
			$image->order      = $order;
			$image->image      = $input_image;
			$image->thumb      = $input_thumb;
			$image->save();

			with(App::make('Change'))->log_relation_change($image, $old_array, array('order', 'image', 'thumb'), $changes);
		}

		// Delete all images not in input
		foreach ($images as $image) {
			if (!in_array($image->id, $input_ids)) {
				with(App::make('Change'))->log_relation_deletion($image, $changes);
				$image->delete();
			}
		}
	}

	protected function handle_options(&$changes)
	{
		$ProductOption     = App::make('ProductOption');
		$ProductOptionItem = App::make('ProductOptionItem');

		// Get all existing options and option items
		$options    = $ProductOption::where('product_id', $this->id)->get();
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
			$option->product_id = $this->id;
			$option->order      = $order;
			$option->name       = $input_option['name'];
			$option->save();

			with(App::make('Change'))->log_relation_change($option, $old_array, array('order', 'name'), $changes);

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

				with(App::make('Change'))->log_relation_change($item, $old_array, array('order', 'name', 'price', 'qty', 'image'), $changes);
			}
		}

		// Delete all options not in input
		foreach ($options as $option) {
			if (!in_array($option->id, $input_option_ids)) {
				with(App::make('Change'))->log_relation_deletion($option, $changes);
				$option->delete();
			}
		}

		// Delete all option items not in input
		foreach ($items as $item) {
			if (!in_array($item->id, $input_item_ids)) {
				with(App::make('Change'))->log_relation_deletion($item, $changes);
				$item->delete();
			}
		}
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function categories()
	{
		return $this->belongsToMany(App::make('ProductCategory'), 'products_categories_products');
	}
	public function options()
	{
		return $this->hasMany(App::make('ProductOption'))->with('items')->orderBy('order');
	}
	public function images()
	{
		return $this->hasMany(App::make('ProductImage'))->orderBy('order');
	}
	public function related()
	{
		return $this->belongsToMany(App::make('Product'), 'products_related_products', 'related_id')->orderBy('products_related_products.order');
	}
	public function changes()
	{
		$Change = App::make('Change');

		return $Change::where('fmodel', 'Product')
			->where('fid', $this->id)
			->with('user')
			->orderBy('created_at', 'DESC')
			->get();
	}

	///////////////////////////////////////////////
	//              Selected Options             //
	///////////////////////////////////////////////
	/**
	 * Mark an option_item as selected by that option_item's ID.
	 *
	 * @param int $option_item_id
	 */
	public function markSelectedOption($option_item_id, $option_order = 0)
	{
		$this->options->each(function($option) use ($option_item_id, $option_order) {
			$option->items->each(function($option_item) use ($option, $option_item_id, $option_order) {
				$arr = $option_item->toArray();
				$arr['order'] = $option_order;
				if ($option_item->id == $option_item_id) $this->selected_options[$option->name . ':' . $option_item->name] = $arr;
			});
		});
		ksort($this->selected_options);
	}

	/**
	 * Same as markSelectedOption() but takes an array.
	 *
	 * @param array $option_item_id
	 */
	public function markSelectedOptions($options)
	{
		if (!is_array($options)) return false;

		foreach ($options as $option_id=>$option_item_id) {
			$option = $this->options->find($option_id);
			$this->markSelectedOption($option_item_id, $option->order);
		}
	}

	/**
	 * Add custom option(s) to the product in this format:
	 *
	 * $options = array(
	 *     'Size' => array(
	 *	       'name'  => 'Large',
	 *	       'price' => 10,
	 *	       'image' => 'large-shirt.jpg'
	 *     )
	 * );
	 *
	 * @param $options - Array of options
	 */
	public function addCustomOptions($options)
	{
		foreach ($options as $name=>$option) {
			$this->selected_options[$name . ':' . $option['name']] = array(
				'name'     => $option['name'],
				'price'    => isset($option['price']) ? $option['price'] : 0,
				'image'    => isset($option['image']) ? $option['image'] : null
			);
		}
		ksort($this->selected_options);
	}

	/**
	 * We need to override toArray() to make it include the special selected_options array.
	 */
	public function toArray()
	{
		$array = parent::toArray();
		$array['selected_options'] = $this->selected_options;
		return $array;
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		return url('products/' . $this->slug);
	}
	public function link_edit()
	{
		return admin_url('products/edit/' . $this->id);
	}
	public function search($terms)
	{
		return static::where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('name', 'like', $term);
				$query->orWhere('url',  'like', $term);
			}
		})->get();
	}

}