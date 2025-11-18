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
        Schema::create('vehicle_owners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('bfu')->nullable();
            $table->foreignUuid('owner_type_id')->nullable()->constrained();
            $table->foreignUuid('legal_status_id')->nullable()->constrained();
            $table->foreignUuid('identity_id')->nullable()->constrained();
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->foreignUuid('profile_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_owners');
    }
};
