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
        Schema::table('material_braille_contents', function (Blueprint $table) {
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->integer('page_number');
            $table->longText('braille_text');
            $table->longText('original_text')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('line_count')->default(0);
            $table->integer('character_count')->default(0);
            
            $table->index(['material_id', 'page_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_braille_contents', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropIndex(['material_id', 'page_number']);
            $table->dropColumn([
                'material_id',
                'page_number',
                'braille_text',
                'original_text',
                'metadata',
                'line_count',
                'character_count'
            ]);
        });
    }
};