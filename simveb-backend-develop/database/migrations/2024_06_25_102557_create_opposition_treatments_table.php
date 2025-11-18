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
        Schema::create('opposition_treatments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->text('rejected_reason')->nullable();
            $table->foreignUuid('opposition_id')->constrained();
            $table->foreignUuid('treated_by')->constrained('profiles');
            $table->foreignUuid('institution_submit_id')->nullable()->constrained('institutions');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('resubmitted_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('lifting_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opposition_treatments');
    }
};
