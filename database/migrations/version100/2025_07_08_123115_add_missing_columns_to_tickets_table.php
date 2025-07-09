<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('response_time')->nullable()->after('time_solved');
            $table->timestamp('due_date')->nullable()->after('response_time');
            $table->string('priority')->default('medium')->after('status');
            $table->string('user_id')->nullable()->after('reported_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['response_time', 'due_date', 'priority', 'user_id']);
        });
    }
};
