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
        Schema::create('reform_declarations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference');
            $table->foreignUuid('auction_sale_declaration_id')
                ->nullable()
                ->constrained();
            $table->foreignUuid('declarant_id')
                    ->nullable()
                    ->constrained('profiles');
            $table->json('report_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reform_declarations');
    }
};
