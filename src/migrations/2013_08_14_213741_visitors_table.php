<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VisitorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visitors', function(Blueprint $table)
		{
			$table->string('sid', 25);
			$table->string('ip', 15);
			$table->string('page', 50);
			$table->string('useragent', 150);
			$table->integer('user')->nullable();
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
		Schema::drop('visitors');
	}

}
