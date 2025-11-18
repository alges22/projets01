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
        Schema::table('pledges', function (Blueprint $table) {
            $table->renameColumn("institution_submit_id", "institution_emitted_id");
            $table->boolean('is_active')->default(false);
            $table->dropConstrainedForeignId('institution_treat_id');
            $table->dropColumn('affected_to_clerk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->renameColumn("institution_emitted_id", "institution_submit_id");
            $table->dropColumn('is_active');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->foreignUuid('affected_to_clerk')->nullable()->constrained('profiles');
        });
    }
};
