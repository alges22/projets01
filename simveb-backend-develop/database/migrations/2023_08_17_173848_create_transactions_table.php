<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->index()->unique();
            $table->string('payment_reference')->index()->unique();
            $table->unsignedDouble('amount');
            $table->unsignedDouble('fees')->default(0);
            $table->unsignedDouble('total_amount');
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid("order_id")->constrained();
            $table->foreignUuid('payment_provider_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
