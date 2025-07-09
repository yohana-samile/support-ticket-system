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
            $table->unsignedInteger('reopen_history_count')->default(0);
            $table->boolean('satisfaction')->nullable();
            $table->text('feedback_comments')->nullable();
            $table->timestamp('feedback_submitted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('reopen_history_count');
            $table->dropColumn('satisfaction');
            $table->dropColumn('feedback_comments');
            $table->dropColumn('feedback_submitted_at');
        });
    }
};
