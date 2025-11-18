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
        Schema::table('pledge_treatments', function (Blueprint $table) {
            $table->timestamp('affected_to_clerk_at')->nullable();
            $table->foreignUuid('affected_to_clerk')->nullable()->constrained('profiles');
            $table->foreignUuid('treated_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledge_treatments', function (Blueprint $table) {
            $table->dropColumn('affected_to_clerk')->nullable();
            $table->dropColumn('affected_to_clerk_at')->nullable();
            $table->dropColumn('treated_by')->nullable()->change();
        });
    }
};
