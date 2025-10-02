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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama_device');
            $table->string('serial_number')->unique();
            $table->foreignId('lembaga_id')->nullable()->constrained('lembagas')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('User pemilik jika lembaga individu');
            $table->enum('status', ['aktif', 'tidak_aktif', 'maintenance'])->default('tidak_aktif');
            $table->timestamp('last_connection')->nullable();
            $table->text('keterangan')->nullable();
            $table->json('device_info')->nullable()->comment('Info tambahan device seperti versi firmware, dll');
            $table->timestamps();
            
            $table->index(['status', 'lembaga_id']);
            $table->index(['serial_number', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};