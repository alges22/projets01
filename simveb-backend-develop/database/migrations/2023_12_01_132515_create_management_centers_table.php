<?php

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
        Schema::create('management_centers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('manager_title');
            $table->foreignUuid('town_id')->constrained();
            $table->foreignUuid('state_id')->constrained();
            $table->foreignUuid('district_id')->nullable()->constrained();
            $table->foreignUuid('village_id')->nullable()->constrained();
            $table->foreignUuid('staff_id')->constrained('staff');
            $table->foreignUuid('management_center_type_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management_centers');
    }
};
