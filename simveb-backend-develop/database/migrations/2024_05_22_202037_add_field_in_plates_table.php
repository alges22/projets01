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
        Schema::table('plates', function (Blueprint $table) {
            $table->string('rfid')->nullable()->change();

            if (Schema::hasColumn('plates', 'immatriculation_type')) {
                $table->string('immatriculation_type')->nullable()->change();
            }

            if (Schema::hasColumn('plates', 'immatriculation_id')) {
                $table->uuid('immatriculation_id')->nullable()->change();
            }

            $table->foreignUuid('anatt_order_id')->nullable()->constrained('plate_orders');
            $table->foreignUuid('institution_order_id')->nullable()->constrained('plate_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anatt_order_id');
            $table->dropConstrainedForeignId('space_order_id');
        });
    }
};
