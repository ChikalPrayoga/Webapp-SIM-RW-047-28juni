<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_keluargas', function (Blueprint $table) {
            $table->string('no_kk', 16)->primary();
            $table->string('rt_code', 5);
            $table->text('alamat_lengkap');
            $table->string('blok', 10)->nullable();
            $table->string('nomor_rumah', 10)->nullable();
            $table->string('status_kepemilikan_rumah', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_keluargas');
    }
};
