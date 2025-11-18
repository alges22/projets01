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
        Schema::create('pledge_lift_treatments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->text('rejected_reasons')->nullable();
            $table->foreignUuid('pledge_lift_id')->nullable()->constrained('pledge_lifts');
            $table->foreignUuid('treated_by')->nullable()->constrained('profiles');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->string('treated_by_space');
            $table->foreignUuid('institution_remitted_id')->nullable()->constrained('institutions');
            $table->foreignUuid('affected_to_clerk')->nullable()->constrained('profiles');
            $table->timestamp('affected_to_clerk_at')->nullable();
            $table->foreignUuid('affected_to_anatt')->nullable()->constrained('profiles');
            $table->timestamp('affected_to_anatt_at')->nullable();
            $table->timestamp('emitted_at')->nullable();
            $table->timestamp('remitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pledge_lift_treatments');
    }
};
