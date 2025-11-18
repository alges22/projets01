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
        Schema::table('demand_otps', function (Blueprint $table) {
            $table->string('owner_npi')->nullable()->change();
            $table->string('owner_ifu')->nullable();
            $table->string('buyer_ifu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_otps', function (Blueprint $table) {
            $table->dropColumn('owner_ifu');
            $table->dropColumn('buyer_ifu');
        });
    }
};
