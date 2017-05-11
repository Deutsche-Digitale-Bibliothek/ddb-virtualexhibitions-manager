<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOmimUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('omim_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username')->unique();
			$table->string('password');
			$table->string('password_change')->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->string('forname')->nullable();
			$table->string('surename')->nullable();
			$table->string('language')->nullable();
			$table->string('country')->nullable();
			$table->string('city')->nullable();
			$table->string('street')->nullable();
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->string('mobile')->nullable();
			$table->tinyInteger('isroot')->default(0);
			$table->string('state')->default('active');
			$table->timestamp('last_login')->nullable();
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
		Schema::drop('omim_users');
	}

}
