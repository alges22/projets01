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
        Schema::table('gma_vehicles', function (Blueprint $table) {
            $table->foreignUuid('validated_by')->nullable()->constrained('profiles');
            $table->timestamp('validated_at')->nullable();
            $table->foreignUuid('rejected_by')->nullable()->constrained('profiles');
            $table->timestamp('rejected_at')->nullable();
            $table->longText('rejected_reason',)->nullable();
            $table->string('status')->default(Status::recorded->name)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gma_vehicles', function (Blueprint $table) {
            $table->dropColumn('validated_by');
            $table->dropColumn('validated_at');
            $table->dropColumn('rejected_by');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_reason');
        });
    }
};
