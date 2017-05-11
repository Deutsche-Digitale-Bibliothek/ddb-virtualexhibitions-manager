<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastPublishedAtToOmimInstancesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('omim_instances', function(Blueprint $table)
		{
			$table->timestamp('last_published_at')->nullable();
			$table->timestamp('last_unpublished_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('omim_instances', function(Blueprint $table)
		{
			$table->dropColumn('last_published_at');
			$table->dropColumn('last_unpublished_at');
		});
	}

}
