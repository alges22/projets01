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
        Schema::create('demands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->foreignUuid('vehicle_owner_id')->nullable()->constrained();
            $table->foreignUuid('vehicle_id')->nullable()->constrained();
            $table->uuid('active_treatment_id')->nullable()->index();
            $table->foreignUuid('profile_id')->nullable()->constrained();
            $table->timestamp('black_listed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('black_list_verified_at')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid('service_id')->constrained('services');
            $table->string('payment_status')->default(Status::pending->name);
            $table->nullableUuidMorphs('model');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};
