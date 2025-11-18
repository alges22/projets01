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
        Schema::create('plate_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('seller_id')
                ->comment('fournisseur de plaque, null si anatt')
                ->nullable()
                ->constrained('institutions');
            $table->foreignUuid('buyer_id')
                ->comment('acheteur de plaque, null si anatt')
                ->nullable()
                ->constrained('institutions');
            $table->integer('quantity');
            $table->foreignUuid('plate_shape_id')->constrained();
            $table->foreignUuid('plate_color_id')->constrained();
            $table->string('status')->default(Status::pending->name);
            $table->timestamp('validated_at')->nullable();
            $table->foreignUuid('validated_by')->nullable()->constrained('staff');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignUuid('rejected_by')->nullable()->constrained('staff');
            $table->longText('rejected_reason')->nullable();
            $table->string('validation_file')->nullable();
            $table->string('payment_status')->default(Status::pending->name);
            $table->integer('amount')->nullable();
            $table->json('order_data')->nullable();
            $table->string('reference')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->boolean('payment_request_sent')->default(false);
            $table->json('validation_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plate_orders');
    }
};
