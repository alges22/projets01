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
        Schema::create('vehicle_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->string('status');
            $table->string('conductor_name');
            $table->string('conductor_phone');
            $table->foreignUuid('validator_id')->nullable()->constrained('users');
            $table->foreignUuid('rejector_id')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('date');
            $table->longText('comment');
            $table->enum('passage_type',['in','out']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_alerts');
    }
};
