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
        Schema::create('codes', function(Blueprint $table)
        {
            $table->id();
            $table->string('name', 150);
            $table->string('lang', 150)->nullable()->comment('entry to facilitate language translation ');
            $table->integer('is_system_defined')->default(1)->comment('the code defined with this will never be available for editing ');
            $table->timestamps();
        });

        Schema::create('code_values', function(Blueprint $table)
        {
            $table->id();
            $table->foreignId('code_id')->constrained()->onDelete('cascade');
            $table->string('name', 191);
            $table->string('lang', 100)->nullable()->comment('entry to facilitate language translation ');
            $table->text('description')->nullable();
            $table->string('reference', 100)->unique();
            $table->integer('sort');
            $table->integer('isactive')->default(1);

            $table->integer('is_system_defined')->default(0)->comment('Flag to specify if the value is system defined or portal admin defined it i.e. 1 => system defined, 0 => portal admin defined (allow editing) ');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name','code_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_values');
    }
};
