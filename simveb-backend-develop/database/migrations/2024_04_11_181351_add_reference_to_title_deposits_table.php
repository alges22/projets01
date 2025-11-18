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
        Schema::table('title_deposits', function (Blueprint $table) {
            $table->string('reference')->unique();
        });
        Schema::table('title_recoveries', function (Blueprint $table) {
            $table->string('reference')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('title_deposits', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
        Schema::table('title_recoveries', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};
