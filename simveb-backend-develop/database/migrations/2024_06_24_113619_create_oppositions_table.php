<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oppositions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->default(Status::pending->name);
            $table->string('reference')->unique();
            $table->foreignUuid('reason_for_opposition')->constrained('title_reasons');
            $table->foreignUuid('owner_id')->constrained('vehicle_owners');
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('institution_submit_id')->nullable()->constrained('institutions');
            $table->foreignUuid('institution_treat_id')->nullable()->constrained('institutions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oppositions');
    }
};
