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
            // Add discount columns
            $table->decimal('discount_amount', 15, 2)->default(0)->after('subtotal');
            $table->double('discount_percentage', 5, 2)->nullable()->after('discount_amount');
            $table->string('discount_reason')->nullable()->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percentage', 'discount_reason']);
        });
    }
};
