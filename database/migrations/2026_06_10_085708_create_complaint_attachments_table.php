<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->unsignedBigInteger('aspirasi_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('aspirasi_id')->references('aspirasi_id')->on('log_laporan_aspirasis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_attachments');
    }
};
