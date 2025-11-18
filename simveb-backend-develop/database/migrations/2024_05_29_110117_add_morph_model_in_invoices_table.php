<?php

use App\Models\Order\Invoice;
use App\Models\Order\Order;
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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_order_id_foreign');
            $table->renameColumn('order_id', 'model_id');
            $table->string('model_type')->nullable();
        });
        foreach (Invoice::all() as $invoice) {
            $invoice->update(['model_type' => Order::class]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('model_type');
        });
    }
};
