<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('users', function (Blueprint $table) {

            $table->increments('id');
            $table->string('username',50)->unique();;
            $table->string('password',64);
            $table->string('email', 64)->unique();;
            $table->morphs('userable');
            //$table->integer('role_id');
            //$table->integer('profile_id');

            $table->rememberToken();
			$table->nullableTimestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::dropIfExists('users');
	}

}
