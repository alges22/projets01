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
        Schema::create('gmd_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vin')->unique();
            $table->string('customs_reference')->nullable()->unique();
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->foreignUuid('vehicle_id')->nullable()->constrained();
            $table->string('status')->default(Status::submitted->name);
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('validated_by')->nullable()->constrained('profiles');
            $table->timestamp('authored_at');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gmd_vehicles');
    }
};
