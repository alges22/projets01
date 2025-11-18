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
        Schema::table('payment_providers', function (Blueprint $table) {
            $table->dropForeign(['activator_id']);
            $table->dropForeign(['deactivator_id']);
            $table->dropForeign(['author_id']);
            $table->dropColumn(['activator_id', 'deactivator_id', 'author_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_providers', function (Blueprint $table) {
            $table->foreignUuid('activator_id')->nullable()->constrained('profiles');
            $table->foreignUuid('deactivator_id')->nullable()->constrained('profiles');
            $table->foreignUuid('author_id')->nullable()->constrained('profiles');
            $table->string('name')->unique();
        });
    }
};
