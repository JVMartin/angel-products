<?php

return array(
	'stripe' => array(
		'test' => array(
			'secret'      => '',
			'publishable' => ''
		),
		'live' => array(
			'secret'      => '',
			'publishable' => ''
		)
	),
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
		'AdminProductController'         => '\Angel\Products\AdminProductController',
		'ProductController'              => '\Angel\Products\ProductController'
	)
);