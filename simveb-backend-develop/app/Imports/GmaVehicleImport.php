<?php

namespace App\Imports;

use App\Enums\InstitutionTypesEnum;
use App\Models\Institution\Institution;
use App\Models\SimvebFile;
use App\Models\Vehicle\GmaVehicle;
use App\Rules\Demands\ValidVINRule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Services\VehicleService;

class GmaVehicleImport extends AbstractCrudRepository implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;
    private VehicleService $vehicleService;
    private array $declarationFileInfo = [];

    public function __construct(private $declarationFile, private $author)
    {
        parent::__construct(GmaVehicle::class);
        $this->vehicleService = new VehicleService;
    }

    public function rules(): array
    {
        $rules = [
            'vin' => ['required', new ValidVINRule, 'unique:gma_vehicles,vin'],
            'institution' => [
                'nullable',
                'exists:institutions,name',
                function ($attribute, $value, $fail) {
                    $institution = Institution::where('name', $value)->first();
                    if (
                        $institution &&
                        $institution?->type->name != InstitutionTypesEnum::io->name
                        &&
                        $institution?->type->name != InstitutionTypesEnum::ngo->name
                    ) {
                        $fail("Cette institution n'est pas affiliée aux affaires intérieures.");
                    }
                },
            ],
            'customs_reference' => ['nullable',]
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'vin.required' => 'Le champ VIN est obligatoire',
            'vin.unique' => 'Ce VIN existe déja veuillez supprimer cette ligne',
            'institution.exists' => 'Le champ institution n\'est pas reconnu',
            'institution.required' => 'Le champ institution est obligatoire',
        ];
    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $institution = Institution::where('name', $row['institution'])->first();

        $vehicle = $this->vehicleService->getVehicleByVinOrImmatriculation(["vin" => $row['vin']]);

        $gmaVehicle = parent::store([
            'customs_reference'  => $row['customs_reference'],
            'vin' => $row['vin'],
            'vehicle_id' => $vehicle->id,
            'author_id' => $this->author?->id,
            'institution_id' => $institution?->id,
        ]);

        if ($this->declarationFile) {
            if (!$this->declarationFileInfo) {
                $this->declarationFileInfo = $this->saveFile($this->declarationFile, 'declaration_files');
            }

            $gmaVehicle->file()->create([
                'path' => $this->declarationFileInfo,
                'type' => SimvebFile::FILE,
            ]);
        }

        return $gmaVehicle;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
