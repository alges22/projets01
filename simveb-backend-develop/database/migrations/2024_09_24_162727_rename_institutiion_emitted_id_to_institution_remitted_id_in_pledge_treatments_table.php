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
            $table->renameColumn("institution_emitted_id", "institution_remitted_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledge_treatments', function (Blueprint $table) {
            $table->renameColumn("institution_remitted_id", "institution_emitted_id");
        });
    }
};
