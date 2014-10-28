<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnswerCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('answer_comments', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('block_answer_id');
			$table->text('reply');
			$table->enum('user_type',array('user','admin'));
			$table->integer('read')->default(0);
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
		Schema::drop('answer_comments');
	}

}
