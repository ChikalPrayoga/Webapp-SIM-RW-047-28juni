<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_keluargas', function (Blueprint $table) {
            $table->string('nik', 16)->primary();
            $table->string('no_kk', 16);
            $table->foreign('no_kk')->references('no_kk')->on('kartu_keluargas')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('pekerjaan')->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->string('status_hubungan_keluarga', 50);
            $table->string('status_sosio_ekonomi', 50)->nullable();
            $table->string('status_warga', 50)->default('AKTIF');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_keluargas');
    }
};
