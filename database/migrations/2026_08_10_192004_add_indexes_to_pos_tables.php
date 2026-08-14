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
            $table->index('customer_id');
            $table->index('account_id');
            $table->index('transaction_date');
            $table->index('payment_status');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('vendor_id');
            $table->index('account_id');
            $table->index('transaction_date');
            $table->index('payment_status');
        });

        Schema::table('payment_receives', function (Blueprint $table) {
            $table->index('account_id');
            $table->index('date');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('warehouse_id');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['account_id']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['payment_status']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['account_id']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['payment_status']);
        });

        Schema::table('payment_receives', function (Blueprint $table) {
            $table->dropIndex(['account_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['warehouse_id']);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['warehouse_id']);
        });
    }
};
