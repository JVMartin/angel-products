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
		App::bind('Product', function() {
			return new \Angel\Products\Product;
		});
		App::bind('ProductCategory', function() {
			return new \Angel\Products\ProductCategory;
		});
		App::bind('ProductImage', function() {
			return new \Angel\Products\ProductImage;
		});
		App::bind('ProductOption', function() {
			return new \Angel\Products\ProductOption;
		});
		App::bind('ProductOptionItem', function() {
			return new \Angel\Products\ProductOptionItem;
		});

		//-------------------
		// Controllers
		//-------------------
		App::bind('AdminProductCategoryController', function() {
			return new \Angel\Products\AdminProductCategoryController;
		});
		App::bind('AdminProductController', function() {
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
