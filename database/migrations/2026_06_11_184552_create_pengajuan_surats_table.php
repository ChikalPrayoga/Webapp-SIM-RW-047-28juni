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
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id('pengajuan_id');
            $table->string('nik', 16);
            $table->string('nomor_surat', 100)->nullable();
            $table->string('jenis_surat', 50);
            $table->text('keperluan');
            $table->string('current_status', 50)->default('SUBMITTED');
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_selesai')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nik')->references('nik')->on('anggota_keluargas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surats');
    }
};
