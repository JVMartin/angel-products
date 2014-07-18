<?php

return array(
	'bindings' => array(
		'Cart'              => '\Angel\Products\Cart',

		// Models
		'Product'           => '\Angel\Products\Product',
		'ProductCategory'   => '\Angel\Products\ProductCategory',
		'ProductImage'      => '\Angel\Products\ProductImage',
		'ProductOption'     => '\Angel\Products\ProductOption',
		'ProductOptionItem' => '\Angel\Products\ProductOptionItem',

		// Controllers
		'AdminProductCategoryController' => '\Angel\Products\AdminProductCategoryController',
		'AdminProductController'         => '\Angel\Products\AdminProductController'
	)
);