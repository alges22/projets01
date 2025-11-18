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
        Schema::table('accreditations', function (Blueprint $table) {
            $table->renameColumn('authored_by', 'author_id');
            $table->renameColumn('validated_by', 'validator_id');
            $table->renameColumn('rejected_by', 'rejector_id');
        });

        Schema::dropIfExists('accreditation_model');

        Schema::create('accreditables', function (Blueprint $table) {
            $table->foreignUuid('accreditation_id')->constrained('accreditations');
            $table->nullableMorphs('accreditable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accreditations', function (Blueprint $table) {
            $table->renameColumn('author_id', 'authored_by');
            $table->renameColumn('validator_id', 'validated_by');
            $table->renameColumn('rejector_id', 'rejected_by');
        });

        Schema::dropIfExists('accreditables');

        Schema::create('accreditation_model', function (Blueprint $table) {
            $table->foreignUuid('accreditation_id')->constrained('accreditations');
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('permission_id')->nullable()->constrained('permissions');
        });
    }
};
