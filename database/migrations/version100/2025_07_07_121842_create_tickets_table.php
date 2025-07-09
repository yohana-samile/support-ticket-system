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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('reported_customer');
            $table->string('ticket_number');
            $table->string('status');

            $table->timestamp('time_reported')->useCurrent();
            $table->timestamp('time_solved')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('reported_by')->nullable()->constrained('users');

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
        Schema::dropIfExists('tickets');
    }
};
