<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_laporan_aspirasis', function (Blueprint $table) {
            $table->id('aspirasi_id');
            $table->string('nik', 16);
            $table->string('kanal_laporan', 50)->default('WEB');
            $table->text('teks_keluhan');
            
            // AI Fields (Nullable)
            $table->string('ai_category', 50)->nullable();
            $table->string('ai_priority', 50)->nullable();
            $table->text('ai_summary')->nullable();
            $table->decimal('ai_confidence', 5, 2)->nullable();
            
            $table->string('current_status', 50)->default('SUBMITTED');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('nik')->references('nik')->on('anggota_keluargas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_laporan_aspirasis');
    }
};
