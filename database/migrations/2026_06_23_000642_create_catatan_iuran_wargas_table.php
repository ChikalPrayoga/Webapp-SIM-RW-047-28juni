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
        Schema::create('catatan_iuran_wargas', function (Blueprint $table) {
            $table->id('iuran_id');
            
            $table->string('no_kk', 16);
            $table->foreign('no_kk')->references('no_kk')->on('kartu_keluargas')->onDelete('restrict');
            
            $table->unsignedBigInteger('iuran_type_id');
            $table->foreign('iuran_type_id')->references('id')->on('iuran_types')->onDelete('restrict');
            
            $table->decimal('nominal', 15, 2);
            $table->tinyInteger('periode_bulan');
            $table->integer('periode_tahun');
            $table->date('tanggal_pembayaran')->nullable();
            
            $table->unsignedBigInteger('recorded_by_user_id')->nullable();
            $table->foreign('recorded_by_user_id')->references('user_id')->on('users')->onDelete('restrict');
            
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->foreign('approved_by_user_id')->references('user_id')->on('users')->onDelete('restrict');
            
            $table->timestamp('approved_at')->nullable();
            
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->string('payment_proof_path', 255)->nullable();
            $table->text('rejection_notes')->nullable();
            
            $table->timestamps();
            
            // Constraints and Indexing
            $table->unique(['no_kk', 'iuran_type_id', 'periode_bulan', 'periode_tahun'], 'idx_catatan_iuran_warga_unique_periode');
            $table->index(['status', 'no_kk'], 'idx_catatan_iuran_status_kk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('catatan_iuran_wargas', function (Blueprint $table) {
                $table->dropForeign(['no_kk']);
                $table->dropForeign(['iuran_type_id']);
                $table->dropForeign(['recorded_by_user_id']);
                $table->dropForeign(['approved_by_user_id']);
            });
        }
        Schema::dropIfExists('catatan_iuran_wargas');
    }
};
