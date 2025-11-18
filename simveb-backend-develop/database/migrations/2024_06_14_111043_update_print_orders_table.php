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
        Schema::table('print_orders', function (Blueprint $table) {
            $table->dropColumn('immatriculation_type');
            $table->dropForeign('print_orders_author_id_foreign');
            $table->dropColumn('validated_by_anatt_at');
            $table->dropColumn('rejected_by_anatt_at');

            $table->string('reference')->unique();
            $table->timestamp('received_at')->nullable()->comment('Delivery date to vehicle owner');
            $table->timestamp('validated_at')->nullable()->comment('Validation date by ANATT');
            $table->timestamp('rejected_at')->nullable()->commen('Rejection date by ANATT');

            $table->foreignUuid('demand_id')->constrained();
            $table->foreign('immatriculation_id')->references('id')->on('immatriculations');
            $table->foreign('author_id')->references('id')->on('profiles')->comment('Author of the print order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
