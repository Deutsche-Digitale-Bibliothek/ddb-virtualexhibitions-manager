<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOmimInstancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('omim_instances', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('subtitle')->nullable();
			$table->string('slug')->unique();
			$table->string('language');
			$table->tinyInteger('langauge_fallback')->default(0);
			$table->integer('fk_root_instance_id');
			$table->string('state')->dafault('active');
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
		Schema::drop('omim_instances');
	}

}
