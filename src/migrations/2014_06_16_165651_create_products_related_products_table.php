<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsRelatedProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products_related_products', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->integer('product_id')->unsigned();
			$table->integer('related_id')->unsigned();
			$table->integer('order')->unsigned();

			$table->primary(array('product_id', 'related_id'));
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('related_id')->references('id')->on('products')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products_related_products');
	}

}
