This is a module for the [Angel CMS](https://github.com/JVMartin/angel).

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
php artisan asset:publish --package="angel/core"
```

Finally, open up your `app/config/packages/angel/core/config.php` and add the module to the `menu` array:
```php
'menu' => array(
	'Pages'		  => 'pages',
	'Menus'		  => 'menus',
	'Products'	=> 'products', // <--- Add this line
	'Users'		  => 'users',
	'Settings'	=> 'settings'
),
```
