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
        Schema::create('reimmatriculations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demand_id')->nullable()->constrained('demands');
            $table->foreignUuid('reason_id')->constrained('reimmatriculation_reasons');
            $table->string('number')->nullable();
            $table->foreignUuid('immatriculation_id')->nullable()->constrained('immatriculations');
            $table->longText('custom_reason')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid('vehicle_id')->constrained('vehicles');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimmatriculations');
    }
};
