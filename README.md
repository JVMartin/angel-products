Angel Products
--------------
This is an eCommerce module for the [Angel CMS](https://github.com/JVMartin/angel).

Installation
------------
Add the following requirements to your `composer.json` file:
```javascript
"require": {
	"angel/products": "dev-master"
},
```

Issue a `composer update` to install the package.

Add the following service provider to your `providers` array in `app/config/app.php`:
```php
'Angel\Products\ProductsServiceProvider'
```

Issue the following commands:
```bash
php artisan migrate --package="angel/products"   # Run the migrations
php artisan asset:publish angel/products         # Publish the assets
```

Finally, open up your `app/config/packages/angel/core/config.php` and add the module to the `menu` array:
```php
'menu' => array(
	'Pages'     => 'pages',
	'Menus'     => 'menus',
	'Products'  => 'products', // <--- Add this line
	'Users'     => 'users',
	'Settings'  => 'settings'
),
```

...and the menu-linkable models to the `linkable_models` array:
```php
'linkable_models' => array(
	'Page'             => 'pages',
	'Product'          => 'products',			// <--- Add this line
	'ProductCategory'  => 'products/categories'	// <--- Add this line
)
```

Cart Usage
----------
[The cart class](https://github.com/JVMartin/angel-products/blob/master/src/Angel/Products/Cart.php) stores variations of products, based on their selected options, in the session.

Example usage:
```php
$Product = App::make('Product');
$Cart    = App::make('Cart');

// Grab the user's desired product from the database.
$product = $Product::with('options')->findOrFail(Input::get('product_id'));

// Mark the selected option items by their IDs.
foreach (Input::get('selected_options') as $option_item_id) {
	$product->markSelectedOption($option_item_id);
}

// Add the product to the cart in the user's desired quantity.
$key = $Cart->add($product, Input::get('quantity'));

// Echo the total for just this product variation.
echo $Cart->totalByKey($key);

// Echo the total for all cart products.
echo $Cart->total();

```
