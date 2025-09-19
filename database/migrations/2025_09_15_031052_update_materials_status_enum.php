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
        Schema::table('materials', function (Blueprint $table) {
            // Modify the status column to include all status values used in the application
            $table->enum('status', ['draft', 'processing', 'review', 'published', 'archived', 'pending'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Revert to original ENUM values
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft')->change();
        });
    }
};
