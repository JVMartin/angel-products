<?php

Route::get('products/{slug}', 'ProductController@view');
Route::post('cart-add', 'ProductController@cart_add');
Route::get('cart', 'ProductController@cart');

Route::group(array('prefix'=>admin_uri('products'), 'before'=>'admin'), function() {

	$controller = 'AdminProductController';

	Route::get('/', function() {
		Session::reflash();
		return Redirect::to(admin_uri('products/categories'));
	});
	Route::get('add', array(
		'uses' => $controller . '@add'
	));
	Route::post('add', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_add'
	));
	Route::get('edit/{id}', array(
		'uses' => $controller . '@edit'
	));
	Route::post('edit/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_edit'
	));
	Route::post('hard-delete/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@hard_delete'
	));

	Route::group(array('prefix'=>'categories'), function() {

		$controller = 'AdminProductCategoryController';

		Route::get('/', array(
			'uses' => $controller . '@index'
		));
		Route::get('add', array(
			'uses' => $controller . '@add'
		));
		Route::post('add', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_add'
		));
		Route::get('edit/{id}', array(
			'uses' => $controller . '@edit'
		));
		Route::post('edit/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_edit'
		));
		Route::post('delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@delete'
		));
		Route::post('hard-delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@hard_delete'
		));
		Route::get('restore/{id}', array(
			'uses' => $controller . '@restore'
		));
		Route::post('update-tree', array(
			'uses' => $controller . '@update_tree'
		));
		Route::get('show-products/{id}', array(
			'uses' => $controller . '@show_products'
		));
	});
});