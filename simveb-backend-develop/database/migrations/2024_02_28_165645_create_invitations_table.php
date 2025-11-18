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
        Schema::create('invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('npi');
            $table->foreignUuid('space_id')->nullable()->constrained();
            $table->foreignUuid('profile_type_id')->constrained();
            $table->boolean('accepted')->default(false)->comment('If guest accept invitation or not');
            $table->boolean('denied')->default(false)->comment('If invitation is denied.');
            $table->unique(['npi', 'space_id', 'profile_type_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
