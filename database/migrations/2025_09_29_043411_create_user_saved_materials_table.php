<?php
// database/migrations/create_user_saved_materials_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_saved_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
            
            // Prevent duplicate saves
            $table->unique(['user_id', 'material_id']);
            
            // Indexes for performance
            $table->index(['user_id', 'saved_at']);
            $table->index(['material_id', 'saved_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_saved_materials');
    }
};