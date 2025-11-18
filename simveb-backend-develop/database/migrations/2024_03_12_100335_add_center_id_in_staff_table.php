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
        Schema::table('staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('head_organization_id');
            $table->foreignUuid('center_id')->nullable()->constrained('management_centers');
            $table->foreignUuid('profile_id')->nullable()->constrained('profiles');
            $table->foreignUuid('identity_id')->nullable()->change();
            $table->foreignUuid('invitation_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invitation_id');
            $table->dropConstrainedForeignId('center_id');
            $table->foreignUuid('head_organization_id')->nullable()->constrained('organizations');
        });
    }
};
