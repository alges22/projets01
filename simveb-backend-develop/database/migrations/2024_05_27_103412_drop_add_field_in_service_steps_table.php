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
        Schema::dropIfExists('service_steps');
        Schema::create('service_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->nullable()->constrained();
            $table->foreignUuid('step_id')->nullable()->constrained();
            $table->integer('position')->nullable();
            $table->enum('process_type', ProcessTypeEnum::toArray())->default(ProcessTypeEnum::automatic->name);
            $table->integer('duration')->nullable();
            $table->unique(['service_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_steps', function (Blueprint $table) {
            //
        });
    }
};
