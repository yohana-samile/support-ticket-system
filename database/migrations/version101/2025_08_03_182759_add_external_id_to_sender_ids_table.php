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
        Schema::table('sender_ids', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id')->comment('this is id from saas_app');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sender_ids', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });
    }
};
