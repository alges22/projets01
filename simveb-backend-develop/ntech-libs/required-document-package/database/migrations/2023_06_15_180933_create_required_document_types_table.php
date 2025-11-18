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
        Schema::create('required_document_types', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid('document_type_id')->constrained();
            $table->string("relation_type");
            $table->boolean('required')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['relation_type', 'document_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('required_document_types');
    }
};
