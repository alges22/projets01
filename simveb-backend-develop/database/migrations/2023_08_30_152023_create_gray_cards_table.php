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
        Schema::create('gray_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->foreignUuid('immatriculation_id')->constrained();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
            $table->boolean('is_lost')->default(false);
            $table->boolean('is_spoiled')->default(false);
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();
            $table->string('deactivation_reason')->nullable();
            $table->foreignUuid('demand_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('gray_cards',function ($table){
            $table->foreignUuid('old_gray_card_id')->nullable()->constrained('gray_cards');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gray_cards');
    }
};
