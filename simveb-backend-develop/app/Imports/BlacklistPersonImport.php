<?php

namespace App\Imports;

use App\Models\Auth\Profile;
use App\Models\Config\BlacklistPerson;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BlacklistPersonImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function __construct(private readonly Profile|null $authorProfile = null)
    {}

    public function rules(): array
    {
        return [
            'npi' => ['nullable', 'digits:10'],
            'ifu' => ['nullable', 'digits:13'],
            'plate_number' => ['nullable',],
            'vin' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'npi.digits' => 'Le champ NPI doit contenir 10 caractères.',

            'ifu.digits' => 'Le champ NPI doit contenir 13 caractères.',

            'vin.string' => 'Le champ Type de véhicule doit être une chaîne de caractères.',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!empty($row['npi']) || !empty($row['ifu']) || !empty($row['plate_number']) || !empty($row['vin'])) {
            $blacklistPerson = BlacklistPerson::create($row);
            return $blacklistPerson;
        } else {
            return null;
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
