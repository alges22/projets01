<?php

namespace App\Services;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class GeneratePdfService
{
    public static function generatePDF(string $view, array $data, string $filename, string $folder = "pdfs", bool $stream = false)
    {
        try {
            $pdf = SnappyPdf::loadView("$view", $data);
            if ($stream) {
                return $pdf->inline($filename);
            } else {
                $path = public_path("storage/$folder/" . $filename);
                $pdf->save($path);

                return $path;
            }
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('Une erreure s\'est produite'));
        }
    }
}
