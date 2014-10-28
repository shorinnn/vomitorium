<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blocks', function(Blueprint $table) {
			$table->increments('id');
			$table->enum('type',array('text','question','answer','top_skills','dynamic','sortable','category', 'video', 'file', 'report'));
			$table->string('title');
			$table->text('text');
			$table->integer('lesson_id');
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
		Schema::drop('blocks');
	}

}
