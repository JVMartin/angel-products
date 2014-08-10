<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsCategoriesProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products_categories_products', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->integer('category_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->integer('order')->unsigned();

			$table->primary(array('category_id', 'product_id'));
			$table->foreign('category_id')->references('id')->on('products_categories')->onDelete('cascade');
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products_categories_products');
	}

}