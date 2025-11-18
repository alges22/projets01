<?php

namespace App\Imports;

use App\Models\Auth\Profile;
use App\Models\Config\BlacklistVehicle;
use App\Models\Vehicle\Vehicle;
use App\Repositories\BlacklistVehicleRepository;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BlacklistVehicleImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function __construct(private readonly Profile|null $authorProfile = null)
    {
    }

    public function rules(): array
    {
        return [
            'vin' => ['string', 'required', Rule::unique('blacklist_vehicles','vin')],
            'vehicle_id' => ['nullable', 'uuid',  Rule::exists('vehicles', 'id'),],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'vin.required' => 'Ce numéro de chassis de véhicule est obligatoire.',
            'vin.string' => 'Ce numéro de chassis de véhicule doit être une chaîne de caractères.',
            'vin.unique' => 'Ce numéro de chassis de véhicule est obligatoire.',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return (new BlacklistVehicleRepository)->store([
            'vin' => $row['vin'],
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
