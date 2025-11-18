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
        Schema::create('pledge_lifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('pledge_id')->constrained('pledges');
            $table->foreignUuid('institution_emitted_id')->constrained('institutions');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pledge_lifts');
    }
};
