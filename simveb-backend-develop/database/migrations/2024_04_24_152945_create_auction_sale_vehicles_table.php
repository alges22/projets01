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
        Schema::create('auction_sale_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('auction_sale_declaration_id')->contrained();
            $table->foreignUuid('vehicle_id')->contrained();
            $table->unsignedBigInteger('price');
            $table->string('buyer_npi')->nullable()->comment("NPI de l'acheteur");
            $table->string('buyer_ifu')->nullable()->comment("IFU de l'acheteur");
            $table->foreignUuid('buyer_identity_id')->nullable()->contrained('identities')->comment("Identité de l'acheteur");
            $table->foreignUuid('buyer_institution_id')->nullable()->contrained('institutions')->comment("Entreprise de l'acheteur");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_sale_vehicles');
    }
};
