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
        Schema::table('oppositions', function (Blueprint $table) {
            $table->foreignUuid('institution_id')->nullable()->constrained('institutions');
            $table->dropConstrainedForeignId('institution_submit_id');
            $table->dropConstrainedForeignId('institution_treat_id');
            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oppositions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->foreignUuid('institution_submit_id')->nullable()->constrained('institutions');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->dropColumn('is_active');
        });
    }
};
