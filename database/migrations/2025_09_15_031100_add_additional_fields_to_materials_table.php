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
            $table->year('tahun_terbit')->nullable()->after('deskripsi');
            $table->string('penerbit')->nullable()->after('tahun_terbit');
            $table->string('edisi')->nullable()->after('penerbit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['tahun_terbit', 'penerbit', 'edisi']);
        });
    }
};