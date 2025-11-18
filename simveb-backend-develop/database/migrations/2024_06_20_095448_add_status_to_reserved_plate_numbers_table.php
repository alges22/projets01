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
        Schema::table('reserved_plate_numbers', function (Blueprint $table) {
            $table->string('status')->default(Status::pending->name);
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserved_plate_numbers', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('validated_at');
            $table->dropColumn('rejected_at');
        });
    }
};
