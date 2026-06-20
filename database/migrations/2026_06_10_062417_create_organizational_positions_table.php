<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_positions', function (Blueprint $table) {
            $table->id('position_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->enum('position_type', [
                'KETUA_RT', 
                'KETUA_RW', 
                'SEKRETARIS_RW', 
                'BENDAHARA_RW', 
                'SUPER_ADMIN'
            ]);
            $table->string('area_code')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_positions');
    }
};
