<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveUserTypeFromAnswerCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('answer_comments', function(Blueprint $table) {
			$table->dropColumn('user_type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('answer_comments', function(Blueprint $table) {
			$table->enum('user_type',array('user','admin'));
		});
	}

}
