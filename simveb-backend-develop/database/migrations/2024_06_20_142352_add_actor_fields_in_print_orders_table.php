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
        Schema::table('print_orders', function (Blueprint $table) {
            $table->foreignUuid('printer_id')->nullable()->constrained('profiles')->comment('Profile of printer');
            $table->foreignUuid('validator_id')->nullable()->constrained('profiles')->comment('Profile of validator');
            $table->foreignUuid('rejector_id')->nullable()->constrained('profiles')->comment('Profile of rejector');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('printer_id');
            $table->dropConstrainedForeignId('validator_id');
            $table->dropConstrainedForeignId('rejector_id');
        });
    }
};
