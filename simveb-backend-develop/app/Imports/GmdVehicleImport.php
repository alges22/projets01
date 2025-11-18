<?php

namespace App\Imports;

use App\Models\Institution\Institution;
use App\Models\SimvebFile;
use App\Models\Vehicle\GmdVehicle;
use App\Rules\Demands\ValidVINRule;
use App\Services\VehicleService;
use App\Traits\UploadFile;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GmdVehicleImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures, UploadFile;

    private VehicleService $vehicleService;
    private array $declarationFileInfo = [];

    public function __construct(private $declarationFile, private $author)
    {
        $this->vehicleService = new VehicleService;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'vin' => ['required', new ValidVINRule, Rule::unique('gmd_vehicles', 'vin')],
            'institution' => ['nullable', 'exists:institutions,name'],
            'customs_reference' => ['nullable',],
        ];

        return $rules;
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'vin.required' => 'Le champ VIN est obligatoire',
            'vin.unique' => 'Ce vin existe déja veuillez supprimer cette ligne',
            'institution.exists' => 'Le champ institution n\'est pas reconnu',
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

        $gmdVehicle = GmdVehicle::create([
            'customs_reference'  => $row['customs_reference'],
            'vin' => $row['vin'],
            'vehicle_id' => $vehicle->id,
            'author_id' => $this->author?->id,
            'institution_id' => $institution?->id,
            'authored_at' => now(),
        ]);

        if ($this->declarationFile) {
            if (!$this->declarationFileInfo) {
                $this->declarationFileInfo = $this->saveFile($this->declarationFile, 'declaration_files');
            }

            $gmdVehicle->file()->create([
                'path' => $this->declarationFileInfo,
                'type' => SimvebFile::FILE,
            ]);
        }

        return $gmdVehicle;
    }

    /**
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }
}
