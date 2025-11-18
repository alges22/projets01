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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->change();
            $table->dropConstrainedForeignId('order_id');
            //$table->foreignUuid('payment_provider_id')->nullable()->constrained();
            $table->uuidMorphs('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropMorphs('model');
            $table->foreignUuid('order_id')->constrained();

        });
    }
};
