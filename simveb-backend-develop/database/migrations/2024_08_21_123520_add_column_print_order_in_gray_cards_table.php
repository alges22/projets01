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
        Schema::table('gray_cards', function (Blueprint $table) {
            $table->foreignUuid('print_order_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gray_cards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('print_order_id');
        });
    }
};
