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
        Schema::table('rooms', function (Blueprint $table) {
            // Change name and description to TEXT to store JSON translations
            $table->text('name')->change();
            $table->text('description')->nullable()->change(); // Assuming description can be nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Revert back to original types (adjust if original types were different)
            // Note: Reverting from TEXT to VARCHAR might truncate data if translations are long.
            $table->string('name')->change(); 
            $table->string('description')->nullable()->change(); 
        });
    }
};
