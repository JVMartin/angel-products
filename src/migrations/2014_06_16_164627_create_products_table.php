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
			$table->text('html');
			$table->text('plaintext');
			$table->boolean('inventory');
			$table->integer('qty')->unsigned()->default(20);
			$table->decimal('price', 9, 2);
			$table->decimal('fake_price', 9, 2);
			$table->boolean('new');
			$table->timestamps(); // Adds `created_at` and `updated_at` columns
		});

		if (ToolBelt::mysql_greater(5, 6, 4)) {
			DB::statement('ALTER TABLE `products` ADD FULLTEXT search(`name`, `plaintext`)');
		}
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