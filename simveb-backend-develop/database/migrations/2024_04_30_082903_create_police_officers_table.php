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
        Schema::create('police_officers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('identity_id')->nullable()->constrained();
            $table->foreignUuid('profile_id')->nullable()->constrained();
            $table->foreignUuid('border_id')->nullable()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('police_officers');
    }
};
