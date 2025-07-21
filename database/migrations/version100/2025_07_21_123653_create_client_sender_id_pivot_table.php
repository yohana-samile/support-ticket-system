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
        Schema::create('client_sender_id', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('sender_id_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('sender_id_id')->references('id')->on('sender_ids')->onDelete('cascade');
            $table->unique(['client_id', 'sender_id_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sender_id');
    }
};
