<?php

use App\Enums\Status;
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
        Schema::create('gov_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('vin')->unique();
            $table->string('customs_reference')->nullable()->unique();
            $table->string('owner_npi');
            $table->foreignUuid('profile_id')
                ->nullable()
                ->constrained();
            $table->foreignUuid('institution_id')
                ->nullable()
                ->constrained();
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->string('status')->default(Status::no_reformed->name);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gov_vehicles');
    }
};
