<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function(Blueprint $table)
		{
			$table->id();
			$table->string('name', 191)->unique();
			$table->text('description')->nullable();
            $table->string('display_name')->nullable();
            $table->string('guard_name')->nullable();
			$table->integer('isactive')->default(1)->comment('specify whether the role is for active i.e. 1 is active, 0 no active');
			$table->integer('isadmin')->default(0)->comment('specify whether the role is for administration i.e. 1 is administrative, 0 not');
            $table->string('uid');
            $table->timestamps();
            $table->softDeletes();
		});
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
	}

}
