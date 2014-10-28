<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddQuestionFieldsToBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('blocks', function(Blueprint $table) {
			$table->enum('answer_type',array('Open Ended', 'Multiple Choice','Scale','Skill Select'));
			$table->integer('maximum_choices')->default(0);
			$table->text('choices');
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
			$table->dropColumn('answer_type');
			$table->dropColumn('maximum_choices');
			$table->dropColumn('choices');
		});
	}

}
