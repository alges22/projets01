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
        Schema::create('international_vehicle_registration_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('user_id')->constrained();
            $table->boolean('under_review')->nullable();
            $table->boolean('approved')->nullable();
            $table->boolean('paid')->nullable();
            $table->boolean('ongoing_issuance')->nullable();
            $table->boolean('issued')->nullable();
            $table->boolean('expired')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->integer('validity_period_in_months')->default(60);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('international_vehicule_registration_documents');
    }
};
