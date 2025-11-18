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
            $table->renameColumn("institution_submit_id", "institution_emitted_id");
            $table->renameColumn('clerk_rejected_reasons', 'rejected_reasons');
            $table->renameColumn('submitted_at', 'emitted_at');
            $table->renameColumn('resubmitted_at', 'remitted_at');
            $table->string('treated_by_space')->nullable();
            $table->dropColumn('issued_at');
            $table->dropColumn('anatt_rejected_reasons');
            $table->dropColumn('clerk_rejected_at');
            $table->renameColumn('anatt_rejected_at', 'rejected_at');
            $table->dropColumn('lifting_at');
            $table->foreignUuid('affected_to_institution')->nullable()->constrained('profiles');
            $table->timestamp('affected_to_institution_at')->nullable();
            $table->foreignUuid('affected_to_anatt')->nullable()->constrained('profiles');
            $table->timestamp('affected_to_anatt_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledge_treatments', function (Blueprint $table) {
            $table->renameColumn("institution_emitted_id", "institution_submit_id");
            $table->renameColumn('rejected_reasons', 'clerk_rejected_reasons');
            $table->renameColumn('emitted_at', 'submitted_at');
            $table->renameColumn('remitted_at', 'resubmitted_at');
            $table->dropColumn('treated_by_space');
            $table->timestamp('issued_at')->nullable();
            $table->text('anatt_rejected_reasons');
            $table->timestamp('clerk_rejected_at');
            $table->renameColumn('rejected_at', 'anatt_rejected_at');
            $table->timestamp('lifting_at')->nullable();
            $table->dropConstrainedForeignId('affected_to_institution')->nullable();
            $table->dropColumn('affected_to_institution_at')->nullable();
            $table->dropConstrainedForeignId('affected_to_anatt')->nullable();
            $table->dropColumn('affected_to_anatt_at')->nullable();
        });
    }
};
