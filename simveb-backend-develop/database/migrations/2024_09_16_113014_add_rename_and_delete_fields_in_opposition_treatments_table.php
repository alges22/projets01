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
        Schema::table('opposition_treatments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_submit_id');
            $table->dropConstrainedForeignId('institution_treat_id');
            $table->foreignUuid('institution_id')->nullable()->constrained('institutions');
            $table->renameColumn('submitted_at', 'emitted_at');
            $table->renameColumn('resubmitted_at', 'remitted_at');
            $table->renameColumn('issued_at', 'validated_at');
            $table->renameColumn('lifting_at', 'lifted_at');
            $table->foreignUuid('affected_to_clerk')->nullable()->constrained('profiles');
            $table->foreignUuid('affected_to_judge')->nullable()->constrained('profiles');
            $table->timestamp('affected_to_clerk_at')->nullable();
            $table->timestamp('affected_to_judge_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opposition_treatments', function (Blueprint $table) {
            $table->foreignUuid('institution_submit_id')->nullable()->constrained('institutions');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->dropConstrainedForeignId('institution_id');
            $table->renameColumn('emitted_at', 'submitted_at');
            $table->renameColumn('remitted_at', 'resubmitted_at');
            $table->renameColumn('validated_at', 'issued_at');
            $table->renameColumn('lifted_at', 'lifting_at');
            $table->dropColumn('affected_to_clerk')->nullable();
            $table->dropColumn('affected_to_clerk_at')->nullable();
            $table->dropColumn('affected_to_judge')->nullable();
            $table->dropColumn('affected_to_judge_at')->nullable();
        });
    }
};
