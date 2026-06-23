<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('transaction_number', 30)->unique();
            $table->string('rt_code', 10)->nullable();
            $table->enum('transaction_type', ['INCOME', 'EXPENSE']);
            $table->string('category', 50);
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->date('transaction_date');
            
            // Polymorphic reference
            $table->nullableMorphs('reference');
            
            $table->unsignedBigInteger('adjusted_transaction_id')->nullable();
            $table->foreign('adjusted_transaction_id')->references('transaction_id')->on('financial_transactions')->onDelete('set null');
            
            $table->unsignedBigInteger('adjusted_by_user_id')->nullable();
            $table->foreign('adjusted_by_user_id')->references('user_id')->on('users')->onDelete('restrict');
            
            $table->timestamp('adjusted_at')->nullable();
            
            $table->unsignedBigInteger('created_by_user_id');
            $table->foreign('created_by_user_id')->references('user_id')->on('users')->onDelete('restrict');
            
            $table->timestamps();
            
            // Compound Index
            $table->index(['rt_code', 'transaction_type', 'transaction_date'], 'fin_trx_compound_index');
        });

        // Add check constraint using raw SQL since Laravel's Schema Builder doesn't natively support CHECK constraints across all DBs elegantly in all versions
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE financial_transactions ADD CONSTRAINT chk_amount_positive CHECK (amount > 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $table->dropForeign(['adjusted_transaction_id']);
                $table->dropForeign(['adjusted_by_user_id']);
                $table->dropForeign(['created_by_user_id']);
            });
        }
        Schema::dropIfExists('financial_transactions');
    }
};
