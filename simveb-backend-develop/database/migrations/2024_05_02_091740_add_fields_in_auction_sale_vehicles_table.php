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
        Schema::table('auction_sale_vehicles', function (Blueprint $table) {
            $table->foreignUuid('reform_declaration_id')->nullable()->constrained();
            $table->string('custom_receipt_reference')->nullable();
            $table->json('pickup_order_path')->nullable();
            $table->json('divesting_file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auction_sale_vehicles', function (Blueprint $table) {
            //
        });
    }
};
