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
        Schema::create('space_suspension_lifting_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('space_id')->constrained();
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid('validator_id')->nullable()->constrained('profiles');
            $table->foreignUuid('rejector_id')->nullable()->constrained('profiles');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->longText('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_suspension_lifting_requests');
    }
};
