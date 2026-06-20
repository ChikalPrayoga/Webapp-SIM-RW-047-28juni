<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_change_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('nik', 16);
            $table->foreign('nik')->references('nik')->on('anggota_keluargas')->onDelete('cascade');
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->enum('current_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_change_requests');
    }
};
