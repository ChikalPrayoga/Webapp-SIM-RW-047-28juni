<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_status_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->unsignedBigInteger('aspirasi_id');
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('aspirasi_id')->references('aspirasi_id')->on('log_laporan_aspirasis')->onDelete('cascade');
            $table->foreign('actor_user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_status_histories');
    }
};
