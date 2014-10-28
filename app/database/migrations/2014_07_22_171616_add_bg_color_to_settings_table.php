<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBgColorToSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('settings', function(Blueprint $table) {
			$table->text('tagline_background_color')->after('tagline_foreground_color');
			$table->text('contact_email')->after('tagline_background_color');
			$table->text('contact_name')->after('contact_email');
			$table->text('email_from')->after('contact_name');
			$table->text('email_name')->after('email_from');
			$table->text('domain')->after('email_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('settings', function(Blueprint $table) {
			$table->dropColumn('tagline_background_color');
			$table->dropColumn('contact_email');
			$table->dropColumn('contact_name');
			$table->dropColumn('email_from');
			$table->dropColumn('email_name');
			$table->dropColumn('domain');
		});
	}

}
