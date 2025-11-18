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
        Schema::create('police_officer_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('profile_id')->nullable()->constrained();
            $table->foreignUuid('border_id')->constrained('borders');
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('validator_id')->nullable()->constrained('profiles');
            $table->foreignUuid('rejector_id')->nullable()->constrained('profiles');
            $table->string('status')->default(Status::pending->name);
            $table->text('reject_reason')->nullable();
            $table->timestamp('authored_at');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('police_officer_assignments');
    }
};
