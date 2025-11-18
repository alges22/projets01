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
        Schema::table('gmd_vehicles', function (Blueprint $table) {
            $table->foreignUuid('rejected_by')->nullable()->constrained('profiles');
            $table->timestamp('rejected_at')->nullable();
            $table->longText('rejected_reason',)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gmd_vehicles', function (Blueprint $table) {
            $table->dropColumn('rejected_by');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_reason');
        });
    }
};
