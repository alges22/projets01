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
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn('assigned_to_service_at');
            $table->dropConstrainedForeignId('assigned_to_service_by');

            $table->foreignUuid('organization_id')->nullable()->constrained();
            $table->timestamp('assigned_to_organization_at')->nullable();
            $table->foreignUuid('assigned_to_organization_by')->nullable()->constrained("profiles");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->foreignUuid('service_id')->nullable()->constrained();
            $table->timestamp('assigned_to_service_at')->nullable();
            $table->foreignUuid('assigned_to_service_by')->nullable()->constrained("profiles");

            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn('assigned_to_organization_at');
            $table->dropConstrainedForeignId('assigned_to_organization_by');

        });
    }
};
