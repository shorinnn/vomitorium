<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddScaleFieldsToBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('blocks', function(Blueprint $table) {
			$table->integer('scale_min')->default(0) ;
			$table->string('scale_min_text');
			$table->integer('scale_max')->default(0);
			$table->string('scale_max_text');
			$table->text('scale_entries');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('blocks', function(Blueprint $table) {
			$table->dropColumn('scale_min');
			$table->dropColumn('scale_min_text');
			$table->dropColumn('scale_max');
			$table->dropColumn('scale_max_text');
			$table->dropColumn('scale_entries');
		});
	}

}
