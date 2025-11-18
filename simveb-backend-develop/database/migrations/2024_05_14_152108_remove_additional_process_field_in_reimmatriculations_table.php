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
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('additional_service_id');
            $table->dropColumn('additional_process_type');
            $table->dropColumn('additional_process_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->foreignUuid('additional_service_id')->nullable()->constrained('services');
            $table->nullableUuidMorphs('additional_process');
        });
    }
};
