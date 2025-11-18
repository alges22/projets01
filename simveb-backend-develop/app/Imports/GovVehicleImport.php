<?php

namespace App\Imports;

use App\Models\Institution\Institution;
use App\Models\Vehicle\GovVehicle;
use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Repositories\Vehicle\GoVehicleRepository;

class GovVehicleImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $current_town;

    public function rules(): array
    {
        $rules = [
            'npi' => ['required', new ValideNpiRule],
            'vin' => ['required', new ValidVINRule, 'unique:gov_vehicles,vin'],
            'institution' => ['nullable', 'exists:institutions,name'],
            'customs_reference' => ['nullable', 'unique:gov_vehicles,customs_reference']
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'npi.required' => 'Le champ npi est obligatoire',
            'vin.required' => 'Le champ nom est obligatoire',
            'vin.unique' => 'Ce vin existe déja veuillez supprimer cette ligne',
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
        return (new GoVehicleRepository)->store([
            'owner_npi'  => $row['npi'],
            'customs_reference'  => $row['customs_reference'],
            'vin' => $row['vin'],
            'author_id' => getOnlineProfile()->id,
            'institution_id' => $institution->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
