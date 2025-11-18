<?php

namespace App\Imports;

use App\Models\Config\District;
use App\Models\Config\Town;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DistrictsImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $current_town;

    public function rules(): array
    {
        $town = $this->current_town;
        $rules = [
            'code' => ['required', 'string'],
            'code_commune' => ['required', 'string', 'exists:towns,code'],
        ];
        if ($town) {
            array_merge($rules,
            [
                'nom' => ['required', 'string', Rule::unique('districts','name')->where('town_id', $town->id)],
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
            'code_commune.required' => 'Le champ nom est obligatoire',
            'code_commune.exists' => 'L\' arrondissement correspondant n\'existe pas',
            'nom.unique' => 'Cet arrondissement existe déja veuillez supprimer cette ligne',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $town = Town::where('code', $row['code_commune'])->first();
        $this->current_town = $town;
        return new District([
            'code'  => $row['code'],
            'name' => $row['nom'],
            'town_id' => $town->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
