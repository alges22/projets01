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
            $table->string('plate_status')->nullable();
            $table->string('card_status')->nullable();

            $table->renameColumn('affected_at', 'plate_affected_at');
            $table->renameColumn('printer_id', 'plate_printer_id');
            $table->renameColumn('printed_at', 'plate_printed_at');
            $table->renameColumn('validator_id', 'plate_validator_id');
            $table->renameColumn('rejector_id', 'plate_rejector_id');
            $table->renameColumn('anatt_observations', 'plate_observations');

            $table->timestamp('plate_validated_at')->nullable();
            $table->timestamp('plate_rejected_at')->nullable();

            $table->foreignUuid('card_printer_id')->nullable()->constrained('profiles');
            $table->timestamp('card_printed_at')->nullable();
            $table->foreignUuid('card_validator_id')->nullable()->constrained('profiles');
            $table->timestamp('card_validated_at')->nullable();
            $table->foreignUuid('card_rejector_id')->nullable()->constrained('profiles');
            $table->timestamp('card_rejected_at')->nullable();
            $table->text('card_observations')->nullable();

            $table->dropColumn('received_at');
            $table->dropConstrainedForeignId('immatriculation_id');
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
