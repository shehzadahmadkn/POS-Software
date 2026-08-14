<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0)->after('total_amount');
            $table->decimal('net_amount', 15, 2)->default(0)->after('discount');
        });

        // Backfill: set net_amount = total_amount for existing records (no discount was applied)
        DB::table('purchases')->where('net_amount', 0)->where('total_amount', '>', 0)
            ->update(['net_amount' => DB::raw('total_amount')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['discount', 'net_amount']);
        });
    }
};
