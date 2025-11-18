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
        Schema::table('title_reasons', function (Blueprint $table) {
            $table->foreignUuid('reason_type')->nullable()->constrained('title_reason_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('title_reasons', function (Blueprint $table) {
            $table->dropColumn('reason_type');
        });
    }
};
