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
        Schema::create('gray_card_duplicates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable()->unique();
            $table->string('reason');
            $table->foreignUuid('old_gray_card_id')->nullable()->constrained('gray_cards');
            $table->foreignUuid('new_gray_card_id')->nullable()->constrained('gray_cards');
            $table->foreignUuid('demand_id')->constrained();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gray_card_duplicates');
    }
};
