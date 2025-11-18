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
        Schema::table('oppositions', function (Blueprint $table) {
            $table->foreignUuid('treatment_id')->nullable()->constrained('opposition_treatments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oppositions', function (Blueprint $table) {
            $table->dropColumn('treatment_id');
        });
    }
};
