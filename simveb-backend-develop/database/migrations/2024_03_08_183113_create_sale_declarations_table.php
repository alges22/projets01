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
        Schema::create('sale_declarations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable()->unique();
            $table->foreignUuid('new_owner_id')->nullable()->constrained('vehicle_owners');
            $table->foreignUuid('vehicle_owner_id')->nullable()->constrained();
            $table->foreignUuid('vehicle_id')->nullable()->constrained();
            $table->string('new_owner_npi')->nullable();
            $table->string('new_owner_ifu')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid('demand_id')->constrained();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_declarations');
    }
};
