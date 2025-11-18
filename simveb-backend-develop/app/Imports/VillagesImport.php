<?php

namespace App\Imports;

use App\Models\Config\District;
use App\Models\Config\Village;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VillagesImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $current_district;

    public function rules(): array
    {
        $district = $this->current_district;
        $rules = [
            'code' => ['required', 'string'],
            'code_arrondissement' => ['required', 'string', 'exists:districts,code'],
        ];
        if ($district) {
            array_merge($rules,
            [
                'nom' => ['required', 'string', Rule::unique('villages','name')->where('district_id', $district->id)],
            ]);
        }
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'code.required' => 'Le champ code est obligatoire',
            'code.unique' => 'Ce code existe déja',
            'code.string' => 'Le champ code doit être une chaîne de caractères',
            'nom.string' => 'Le champ nom doit être une chaîne de caractères',
            'nom.required' => 'Le champ nom est obligatoire',
            'code_arrondissement.required' => 'Le champ nom est obligatoire',
            'code_arrondissement.exists:district,code' => 'Le district correspondant n\'existe pas',
            'nom.unique' => 'Ce village existe déja veuillez supprimer cette ligne',
        ];
    }



    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $district = District::where('code', $row['code_arrondissement'])->first();
        $this->current_district = $district;

        return new Village([
            'code'  => $row['code'],
            'name' => $row['nom'],
            'district_id' => $district->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
