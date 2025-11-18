<?php

namespace App\Imports;

use App\Models\Config\Town;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class TownsImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;


    public function rules(): array
    {
        $rules = [
            'code' => ['required', 'string'],
            'nom' => ['required', 'string', Rule::unique('towns','name')],
        ];
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
            'nom.unique' => 'Cette commune existe déja veuillez supprimer cette ligne',
        ];
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Town([
            'code'  => $row['code'],
            'name' => $row['nom'],
        ]);
    }


    public function headingRow(): int
    {
        return 1;
    }
}
