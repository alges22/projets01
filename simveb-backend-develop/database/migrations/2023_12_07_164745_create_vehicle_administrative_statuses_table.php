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
        Schema::create('vehicle_administrative_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('situation_type',['gage','opposition']);
            $table->string('motif')->nullable();
            $table->string('declaration_code')->unique();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('declarant_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->foreignUuid('demand_id')->constrained();
            $table->string('status')->default(Status::pending->name);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_administrative_statuses');
    }
};
