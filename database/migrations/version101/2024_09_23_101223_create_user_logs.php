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
        Schema::create('user_logs', function(Blueprint $table)
        {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_log_cv_id')
                ->constrained('code_values')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->boolean('has_remember')->nullable();
            $table->text('browser')->nullable();
            $table->text('browser_version')->nullable();
            $table->text('device')->nullable();
            $table->text('platform')->nullable();
            $table->text('platform_version')->nullable();
            $table->boolean('isdesktop')->nullable();
            $table->boolean('isphone')->nullable();
            $table->boolean('isrobot')->nullable();
            $table->text('robot_name')->nullable();
            $table->string('username', 30)->nullable();
            $table->boolean('ismobile')->nullable();
            $table->boolean('istablet')->nullable();
            $table->text('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
