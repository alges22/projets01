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
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('can_be_demanded')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_child')->default(false);
            $table->timestamp('deactived_at')->nullable();
            $table->foreignUuid('deactived_by')->nullable()->constrained('profiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('can_be_demanded');
            $table->dropColumn('is_active');
            $table->dropColumn('is_child');
            $table->dropColumn('deactived_at');
            $table->dropColumn('deactived_by');
        });
    }
};
