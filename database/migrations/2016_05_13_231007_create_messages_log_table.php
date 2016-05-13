<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('created');
			$table->char('message_sid', 34);
			$table->char('account_sid', 34);
			$table->string('from', 50);
			$table->string('to', 50);
			$table->text('body', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages_log');
	}

}
