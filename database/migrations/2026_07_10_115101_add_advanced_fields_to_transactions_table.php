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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('delivery_charges', 15, 2)->default(0)->after('discount');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete()->after('payment_method');
            $table->date('transaction_date')->nullable()->after('payment_method');
            $table->text('note')->nullable()->after('payment_status');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('delivery_charges', 15, 2)->default(0)->after('total_amount');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete()->after('payment_status');
            $table->date('transaction_date')->nullable()->after('payment_status');
            $table->text('note')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn(['delivery_charges', 'account_id', 'transaction_date', 'note']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn(['delivery_charges', 'account_id', 'transaction_date', 'note']);
        });
    }
};
