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
            $table->string('ticket_number');
            $table->string('title');
            $table->text('description');
            $table->string('status')->index();
            $table->string('priority')->index();
            $table->string('mobile_operator')->nullable();

            $table->foreignId('saas_app_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->nullOnDelete();
            $table->foreignId('sub_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tertiary_topic_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('User who reported ticket, can be different from client');

            $table->foreignId('payment_channel_id')->nullable()->constrained('payment_channels')->nullOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('sender_ids')->nullOnDelete();

            $table->timestamp('time_reported')->useCurrent();
            $table->timestamp('time_solved')->nullable();
            $table->integer('response_time')->nullable();

            $table->unsignedInteger('reopen_history_count')->default(0);
            $table->boolean('satisfaction')->default(false);
            $table->timestamp('feedback_submitted_at')->nullable();

            $table->timestamp('escalated_at')->nullable();
            $table->text('escalation_reason')->nullable();

            $table->string('uid');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['saas_app_id', 'status']);
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
