<?php

namespace App\Services;

use App\Models\Order\Invoice;
use Illuminate\Support\Str;
use LaravelPdf;

class InvoiceService
{
    /**
     * @param mixed $model
     * @param bool $stream
     * @return mixed
     */
    public function generate($model, $stream = false): mixed
    {
        $invoice = $model instanceof Invoice ? $model : $model->invoice;

        $modelName = Str::kebab(class_basename($invoice->model));

        $template = 'invoices/' . $modelName . '-invoice';

        $fileName = 'facture-' . $modelName . '-' . $invoice->reference . time() . '.pdf';

        return $stream ?
            LaravelPdf::loadView($template, compact('invoice'))->setPaper('a4', 'landspace')->stream($fileName)
            :
            LaravelPdf::loadView($template, compact('invoice'))->setPaper('a4', 'landspace')->download($fileName);
    }
}
