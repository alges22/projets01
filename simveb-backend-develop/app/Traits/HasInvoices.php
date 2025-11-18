<?php
namespace App\Traits;

use App\Models\Order\Invoice;

trait HasInvoices
{
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'model', 'model_type');
    }

    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'model', 'model_type');
    }
}
