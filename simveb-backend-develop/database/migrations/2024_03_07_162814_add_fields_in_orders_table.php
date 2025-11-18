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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->foreignUuid('profile_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignUuid('owner_id')->constrained('vehicle_owners');
            $table->dropConstrainedForeignId('institution_id');
            $table->dropConstrainedForeignId('profile_id');
        });
    }
};
