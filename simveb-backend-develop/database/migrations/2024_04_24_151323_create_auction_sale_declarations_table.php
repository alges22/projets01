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
        Schema::create('auction_sale_declarations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('auctioneer_id')->constrained('profiles');
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->json('report_path')->nullable();
            $table->json('officials')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_sale_declarations');
    }
};
