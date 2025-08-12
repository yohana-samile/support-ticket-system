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
        Schema::create('stickers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('sticker owner')->constrained('users')->onDelete('cascade');
            $table->text('note');
            $table->string('color_code', 20)->default('#FFD700');

            $table->dateTime('remind_at')->nullable();
            $table->boolean('is_private')->default(true);
            $table->boolean('is_for_all')->default(false)->comment('send to everyone');
            $table->string('status')->comment('eg active, done, archived');

            $table->string('uid');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stickers');
    }
};
