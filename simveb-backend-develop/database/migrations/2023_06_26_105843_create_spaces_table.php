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
        Schema::create('spaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('request_id')->nullable()->constrained('space_registration_requests');
            $table->foreignUuid('profile_type_id')->constrained('profile_types');
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**A
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
