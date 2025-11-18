<?php

namespace App\Services;

use App\Exports\ExportErrorFile;
use Maatwebsite\Excel\HeadingRowImport;
use App\Notifications\NotificationSender;
use Illuminate\Support\Facades\Notification;
use App\Consts\NotificationNames;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Maatwebsite\Excel\Facades\Excel;

class HandleExcelErrorsService {

    public function HandleExcelErrors ($import, $file, $modelName) {
        $headerRow = [];
        $rowValues = [];
        try
        {
            $e = $import;
            $errors = [];
            foreach ($e->failures() as $failure) {
                if (!$headerRow) {
                    $headerRow = (new HeadingRowImport())->toArray($file)[0][0];
                    $headerRow = array_merge($headerRow, ['Erreurs']);
                    $errors[] = $headerRow;
                }
                if ($rowValues == $failure->values()) {
                    $lastErrorIndex = count($errors) - 1;
                    $errors[$lastErrorIndex]['Erreurs'] .= "; " . $failure->errors()[0];
                } else {
                $rowValues = $failure->values();
                $errorMessages = $failure->errors();
                $errorDetails = array_merge($rowValues, $errorMessages);

                $errors[] = array_combine($headerRow, $errorDetails);
                }
            }
            $filename =  class_basename($modelName) . '_' . Carbon::now()->toDateTimeString() . '.xlsx';
            $filepath = "import-errors/import/" . $filename;

            Excel::store(new ExportErrorFile($errors), $filepath, 'public');

            /* sendMail(
                null,
                getOnlineProfile()->identity,
                NotificationNames::IMPORT_HAS_ERRORS,                       
                ['attachment' => $filepath,]
            ); */

            return public_path('storage/'.$filepath);

        } catch (\Exception $exception)
        {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('Une erreure s\'est produite'));
        }

    }
}
