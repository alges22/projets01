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
        Schema::table('vehicle_alerts', function (Blueprint $table) {
            $table->foreignUuid('border_id')->nullable()->constrained('borders');
            $table->renameColumn('recorder_officer_id', 'author_id');
            $table->renameColumn('canceler_officer_id', 'cancelor_id');
            $table->renameColumn('recorded_at', 'authored_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_alerts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('border_id');
            $table->renameColumn('author_id', 'recorder_officer_id');
            $table->renameColumn('cancelor_id', 'canceler_officer_id');
            $table->renameColumn('authored_at', 'recorded_at');
        });
    }
};
