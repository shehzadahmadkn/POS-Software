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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('account_id');
            $table->index('transaction_date');
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->index('from_account_id');
            $table->index('to_account_id');
            $table->index('date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('account_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['account_id']);
            $table->dropIndex(['transaction_date']);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex(['from_account_id']);
            $table->dropIndex(['to_account_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['account_id']);
            $table->dropIndex(['date']);
        });
    }
};
