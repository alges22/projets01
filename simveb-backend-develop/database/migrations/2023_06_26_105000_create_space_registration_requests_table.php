<?php

use App\Enums\SpaceTypesEnum;
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
        Schema::create('space_registration_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('profile_type_id')->nullable()->constrained('profile_types');
            $table->foreignUuid('institution_id')->constrained();
            $table->string('first_member_npi',10);
            $table->enum('status', [
                Status::pending->name,
                Status::validated->name,
                Status::rejected->name,
            ])->default(Status::pending->name);
            $table->timestamp('validated_at')->nullable();
            $table->foreignUuid('validator_id')->nullable()->constrained('profiles');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignUuid('rejector_id')->nullable()->constrained('profiles');
            $table->text('reject_reason')->nullable();
            $table->foreignUuid('creator_id')->nullable()->constrained('profiles');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_registration_requests');
    }
};
