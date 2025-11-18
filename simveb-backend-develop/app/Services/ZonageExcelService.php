<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Validators\ValidationException as ValidatorsValidationException;

class ZonageExcelService
{
    public function __construct(private HandleExcelErrorsService $service)
    {
    }
	private $fileErrorPath = '';
    public function import(Request $request, $modelName)
    {
        try
        {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);
    
            $file = $request->file('file');
    
            $importClass = 'App\Imports\\' . $modelName . 'sImport';
    
            if (!class_exists($importClass)) {
                return response()->json(['error' => 'Modele introuvable.'], 404);
            }
            
            $import = new $importClass();
            $import->import($file);
    
            if ($import->failures()) {
                $this->fileErrorPath = $this->service->HandleExcelErrors($import, $file, $modelName);
    
                return response()->json(['message' => 'Importation effectuée avec quelques erreurs, vous pouvez télécharger le fichier d\'erreur ou le retrouver dans votre boite mail.', 'ErrorFile' => $this->fileErrorPath]);
            }
    
            return response()->json(['message' => 'Importation réussie.']);
        }catch (\Exception $exception)
        {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('l\'importation a échoué'));
        }
    }

    public function export($modelName)
    {
        try
        {
            $exportClass = "App\Exports\\{$modelName}sExport";
            return Excel::download(new $exportClass, $modelName .'_template.xlsx');
        }catch (\Exception $exception)
        {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

}
