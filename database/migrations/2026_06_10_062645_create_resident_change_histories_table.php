<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_change_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('request_id')->constrained('resident_change_requests', 'request_id')->onDelete('cascade');
            $table->foreignId('actor_user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_change_histories');
    }
};
