<?php namespace Angel\Products;

use Illuminate\Support\ServiceProvider;
use App;

class ProductsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('angel/products');

		include __DIR__ . '../../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//-------------------
		// Models
		//-------------------
		App::singleton('Product', function() {
			return new \Angel\Products\Product;
		});
		App::singleton('ProductCategory', function() {
			return new \Angel\Products\ProductCategory;
		});
		App::singleton('ProductImage', function() {
			return new \Angel\Products\ProductImage;
		});
		App::singleton('ProductOption', function() {
			return new \Angel\Products\ProductOption;
		});
		App::singleton('ProductOptionItem', function() {
			return new \Angel\Products\ProductOptionItem;
		});

		//-------------------
		// Controllers
		//-------------------
		App::singleton('AdminProductCategoryController', function() {
			return new \Angel\Products\AdminProductCategoryController;
		});
		App::singleton('AdminProductController', function() {
			return new \Angel\Products\AdminProductController;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
