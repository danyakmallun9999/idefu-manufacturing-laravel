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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('order_quantity_snapshot', 15, 2)->nullable()->after('remaining_amount');
            $table->decimal('order_unit_price_snapshot', 15, 2)->nullable()->after('order_quantity_snapshot');
            $table->decimal('order_total_snapshot', 15, 2)->nullable()->after('order_unit_price_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'order_quantity_snapshot',
                'order_unit_price_snapshot',
                'order_total_snapshot',
            ]);
        });
    }
};

