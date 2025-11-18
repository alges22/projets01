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
        Schema::create('accreditations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('rejected_reason')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid('receiver_id')->constrained('profiles');
            $table->foreignUuid('authored_by')->constrained('profiles');
            $table->foreignUuid('validated_by')->nullable()->constrained('profiles');
            $table->foreignUuid('rejected_by')->nullable()->constrained('profiles');
            $table->timestamp('authored_at');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('accreditation_model', function (Blueprint $table) {
            $table->foreignUuid('accreditation_id')->constrained('accreditations');
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('permission_id')->nullable()->constrained('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_model');
        Schema::dropIfExists('accreditations');
    }
};
