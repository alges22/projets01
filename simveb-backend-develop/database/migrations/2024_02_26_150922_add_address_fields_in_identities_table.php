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
        Schema::table('identities', function (Blueprint $table) {
            $table->foreignUuid('state_id')->nullable()->constrained();
            $table->foreignUuid('town_id')->nullable()->constrained();
            $table->foreignUuid('district_id')->nullable()->constrained();
            $table->foreignUuid('village_id')->nullable()->constrained();
            $table->string('house')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('identities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('town_id');
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('village_id');
            $table->dropColumn('house')->nullable();
        });
    }
};
