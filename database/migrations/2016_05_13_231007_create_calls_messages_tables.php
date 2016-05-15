<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCallsMessagesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('calls_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('call_sid', 34)->index('call_sid');
			$table->char('account_sid', 34);
			$table->string('from', 50);
			$table->string('to', 50);
			$table->dateTime('created');
			$table->integer('duration')->unsigned()->nullable();
			$table->dateTime('thanked')->nullable();
			$table->index(['thanked','duration','created'], 'thanked');
		});

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
		Schema::drop('calls_log');

		Schema::drop('messages_log');
	}

}
