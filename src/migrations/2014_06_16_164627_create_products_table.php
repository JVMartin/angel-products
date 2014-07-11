<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('slug')->unique();
			$table->string('name');
			$table->string('size');
			$table->text('description');
			$table->integer('category_id')->unsigned()->nullable();
			$table->float('price');
			$table->float('fake_price');
			$table->boolean('new');
			$table->timestamps(); // Adds `created_at` and `updated_at` columns

			$table->foreign('category_id')->references('id')->on('products_categories')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}