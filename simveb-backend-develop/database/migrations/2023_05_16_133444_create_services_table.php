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
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->foreignUuid('type_id')->constrained('service_types');
            $table->unsignedInteger('duration')->comment('in days')->nullable();
            $table->unsignedDouble('cost')->nullable();
            $table->longText('procedures')->nullable();
            $table->longText('extract')->nullable();
            $table->longText('who_can_apply')->nullable();
            $table->string('link')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignUuid('published_by')->nullable()->constrained('users');
            $table->foreignUuid('target_organization_id')->nullable()->constrained('organizations');
            $table->uuid('parent_service_id')->nullable()->index();
            $table->foreignUuid('vehicle_category_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
