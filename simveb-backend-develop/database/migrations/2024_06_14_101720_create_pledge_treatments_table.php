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
        Schema::create('pledge_treatments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->text('reasons')->nullable();
            $table->foreignUuid('pledge_id')->constrained('pledges');
            $table->foreignUuid('validator_id')->constrained('profiles');
            $table->foreignUuid('institution_submit_id')->nullable()->constrained('institutions');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pledge_treatments');
    }
};
