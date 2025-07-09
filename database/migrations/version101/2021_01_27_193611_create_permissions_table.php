<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function(Blueprint $table)
		{
			$table->id();
            $table->foreignId('permission_group_id')->constrained()->onDelete('cascade');
			$table->string('name', 150);
			$table->text('display_name');
			$table->text('description')->nullable();
            $table->string('guard_name')->nullable();
			$table->integer('ischecker')->default(0)->comment('set whether this permission needs a second person check ');
			$table->integer('isadmin')->default(0)->comment('specify whether the role is for administration i.e. 1 is administrative, 0 not');
            $table->integer('isactive')->default(1)->comment('specify whether the role is for active i.e. 1 is active, 0 no active');
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
		Schema::drop('permissions');
	}

}
