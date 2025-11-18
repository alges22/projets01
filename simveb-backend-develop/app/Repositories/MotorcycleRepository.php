<?php

namespace App\Repositories;

use App\Imports\MotorcycleImport;
use App\Models\Motorcycle;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\HandleExcelErrorsService;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MotorcycleRepository extends AbstractCrudRepository
{
  private HandleExcelErrorsService $service;
  private $fileErrorPath = '';
    public function __construct()
    {
      parent::__construct(Motorcycle::class);
      $this->service = new HandleExcelErrorsService;
    }
    public function importMotorcycles($request): array
    {
        try
        {
            $file = $request['file'];

            $import = new MotorcycleImport($request['customs_reference']);
            $import->import($file);

            if (isset($import->failures()[0])) {
                $this->fileErrorPath = $this->service->HandleExcelErrors($import, $file, 'gov_vehicle');

                return ['message' => 'Importation effectuée avec quelques erreurs, vous pouvez télécharger le fichier d\'erreur ou le retrouver dans votre boite mail.', 'ErrorFile' => $this->fileErrorPath];
            }

            return ['message' => "Importation réussie."];
        }catch (Exception $exception)
        {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('L\'importation a échoué'));
        }
    }
}
