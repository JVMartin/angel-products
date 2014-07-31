<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->decimal('total', 9, 2);
			$table->text('cart');
			$table->text('shipping_address');
			$table->text('billing_address');
			$table->string('charge_id');
			$table->integer('user_id')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
