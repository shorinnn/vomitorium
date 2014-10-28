<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PrepopulateSkillsToSkillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                $skill = new Skill();
                $skill->type = 'Functional Skills';
                $skill->save();
                $skill = new Skill();
                $skill->type = 'Personality Skills';
                $skill->save();
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('skills')->where('type','Functional Skills')->delete();
		DB::table('skills')->where('type','Personality Skills')->delete();
	}

}
