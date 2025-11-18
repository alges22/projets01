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
        Schema::create('print_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->default(Status::pending->name);
            $table->uuidMorphs('immatriculation');
            $table->foreignUuid('institution_id')->nullable()->constrained();
            $table->foreignUuid('author_id')->nullable()->constrained('users');
            $table->timestamp('affected_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('validated_by_anatt_at')->nullable();
            $table->timestamp('rejected_by_anatt_at')->nullable();
            $table->longText('anatt_observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_orders');
    }
};
