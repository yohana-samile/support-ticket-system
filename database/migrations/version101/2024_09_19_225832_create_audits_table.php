<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->string('user_type', 191)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('event', 191);
            $table->string('auditable_type', 191);
            $table->bigInteger('auditable_id');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent', 191)->nullable();
            $table->string('tags', 191)->nullable();
            $table->timestamps();
            $table->index(['user_id','user_type']);
            $table->index(['auditable_type','auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->drop($table);
    }
}
