<?php

use App\Enums\SpaceTemplateEnum;
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
        Schema::table('space_registration_requests', function (Blueprint $table) {
            $table->string('template')->default(SpaceTemplateEnum::default->name);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('space_registration_requests', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
