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
        Schema::table('management_centers', function (Blueprint $table) {
            $table->foreignUuid('responsible_id')->nullable()->constrained('profiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management_centers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responsible_id');
        });
    }
};
