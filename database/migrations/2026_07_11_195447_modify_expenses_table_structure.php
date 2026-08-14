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
        Schema::table('expenses', function (Blueprint $table) {
            // Drop foreign keys first if any
            $table->dropForeign(['location_id']);
            $table->dropForeign(['user_id']);
            
            // Drop old columns
            $table->dropColumn(['location_id', 'user_id', 'category', 'description']);

            // Add new columns
            $table->string('reference_no')->unique()->after('id');
            $table->foreignId('expense_category_id')->after('reference_no')->constrained('expense_categories')->onDelete('cascade');
            $table->foreignId('account_id')->after('expense_category_id')->constrained('accounts')->onDelete('cascade');
            $table->date('date')->after('account_id');
            $table->text('note')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
