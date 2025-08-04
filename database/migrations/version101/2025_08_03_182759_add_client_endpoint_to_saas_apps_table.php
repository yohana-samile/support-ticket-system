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
        Schema::table('saas_apps', function (Blueprint $table) {
            $table->string('client_endpoint')->nullable()->after('abbreviation')->comment('this endpoint return json of clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saas_apps', function (Blueprint $table) {
            $table->dropColumn('client_endpoint');
        });
    }
};
