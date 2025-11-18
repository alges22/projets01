<?php

use App\Models\Config\GeographicalArea;
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
        Schema::create('geographical_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->enum('type',[
                GeographicalArea::CITY,
                GeographicalArea::REGION,
                GeographicalArea::COUNTRY,
            ])->default(GeographicalArea::CITY);
            $table->string('code')->unique();
            $table->longText('authorized_registration_format')->nullable();
            $table->longText('validation_criteria')->nullable();
            $table->longText('restrictions_or_special_requirements')->nullable();
            $table->json('staff_ids');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geographical_areas');
    }
};
