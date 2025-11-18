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
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('resubmitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('anatt_rejected_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('clerk_rejected_at')->nullable();
            $table->timestamp('lifting_at')->nullable();
            $table->text('anatt_rejected_reasons')->nullable();
            $table->renameColumn('reasons', 'clerk_rejected_reasons');
            $table->renameColumn('validator_id', 'treated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledge_treatments', function (Blueprint $table) {
            $table->dropColumn('submitted_at')->nullable();
            $table->dropColumn('resubmitted_at')->nullable();
            $table->dropColumn('validated_at')->nullable();
            $table->dropColumn('anatt_rejected_at')->nullable();
            $table->dropColumn('issued_at')->nullable();
            $table->dropColumn('clerk_rejected_at')->nullable();
            $table->dropColumn('lifting_at')->nullable();
            $table->dropColumn('anatt_rejected_reasons')->nullable();
            $table->renameColumn('clerk_rejected_reasons', 'reasons');
            $table->renameColumn('treated_by', 'validator_id');
        });
    }
};
