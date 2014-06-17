<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products_categories', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name');
			$table->string('image');
			$table->integer('order')->unsigned();
			$table->integer('parent_id')->unsigned()->nullable();
			$table->timestamps();

			$table->foreign('parent_id')->references('id')->on('products_categories')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products_categories');
	}

}