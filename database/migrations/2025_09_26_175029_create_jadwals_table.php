<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('materi')->nullable(); // Nanti bisa jadi foreign key ke tabel materials
            $table->enum('pengulangan', ['tidak', 'harian', 'mingguan'])->default('tidak');
            $table->enum('status', ['belum_mulai', 'sedang_berlangsung', 'selesai'])->default('belum_mulai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};