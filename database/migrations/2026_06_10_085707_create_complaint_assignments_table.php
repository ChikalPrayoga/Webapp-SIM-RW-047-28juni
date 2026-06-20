<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('aspirasi_id');
            $table->unsignedBigInteger('assigned_by_user_id');
            $table->unsignedBigInteger('assigned_to_user_id');
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('aspirasi_id')->references('aspirasi_id')->on('log_laporan_aspirasis')->onDelete('cascade');
            $table->foreign('assigned_by_user_id')->references('user_id')->on('users');
            $table->foreign('assigned_to_user_id')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_assignments');
    }
};
