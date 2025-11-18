<?php

use App\Enums\ProcessTypeEnum;
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
        Schema::create('actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_step_id')->constrained();
            $table->foreignUuid('permission_service_id')->constrained();
            $table->integer('position')->nullable();
            $table->integer('duration')->nullable();
            $table->enum('process_type', ProcessTypeEnum::toArray())->default(ProcessTypeEnum::automatic->name);
            $table->unique(['service_step_id', 'position']);
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->string('pre_status')->nullable();
            $table->string('post_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
