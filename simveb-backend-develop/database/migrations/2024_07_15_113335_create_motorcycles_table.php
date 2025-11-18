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
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customs_reference');
            $table->string('vin')->unique();
            $table->string('npi')->nullable();
            $table->string('ifu')->nullable();
            $table->foreignUuid('vehicle_id')->nullable()->constrained();
            $table->foreignUuid('institution_id')->constrained();
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('buyer_id')->nullable()->constrained('profiles');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};
