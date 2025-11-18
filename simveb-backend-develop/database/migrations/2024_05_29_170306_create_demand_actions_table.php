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
        Schema::create('demand_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demand_id')->constrained();
            $table->foreignUuid('action_id')->constrained();
            $table->foreignUuid('profile_id')->constrained();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->bigInteger('duration')->nullable()->comment('duration the action taken in second');
            $table->string('status')->nullable()->default(Status::pending->name);
            $table->string('done_status')->nullable()->default(Status::pending->name);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_actions');
    }
};
