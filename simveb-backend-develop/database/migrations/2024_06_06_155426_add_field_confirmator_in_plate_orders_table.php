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
        Schema::table('plate_orders', function (Blueprint $table) {
            $table->foreignUuid('confirmator_id')->nullable()->constrained('profiles');
            $table->timestamp('confirmed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plate_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('confirmator_id');
            $table->dropColumn('confirmed_at');
        });
    }
};
