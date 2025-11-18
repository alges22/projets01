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
        Schema::create('demand_updates_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('old_value');
            $table->string('new_value');
            $table->string('type');
            $table->foreignUuid('demand_id')->constrained('demands');
            $table->boolean('is_validated')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_updates_histories');
    }
};
