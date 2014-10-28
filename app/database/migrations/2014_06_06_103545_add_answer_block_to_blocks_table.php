<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAnswerBlockToBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('blocks', function(Blueprint $table) {
			$table->integer('answer_id')->default(0);
			$table->string('skill_type')->nullable();
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
			$table->dropColumn('answer_id');
			$table->dropColumn('skill_type');
		});
	}

}
