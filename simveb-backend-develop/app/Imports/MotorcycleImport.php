<?php

namespace App\Imports;

use App\Rules\Demands\ValideNpiRule;
use App\Rules\Demands\ValidVINRule;
use App\Services\MotorcycleService;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MotorcycleImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;
    private MotorcycleService $motorcycleService;
    public function __construct(private $customsReference)
    {
        $this->motorcycleService = new MotorcycleService;
        $this->customsReference = $customsReference;
    }


    private $current_town;

    public function rules(): array
    {
        $rules = [
            'vin' => ['required', new ValidVINRule, 'unique:motorcycles,vin'],
            'npi' => ['nullable', new ValideNpiRule],
            'customs_reference' => ['nullable']
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'vin.required' => 'Le champ vin est obligatoire',
            'vin.unique' => 'Ce vin existe déja veuillez supprimer cette ligne',
        ];
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return $this->motorcycleService->store([
            'customs_reference'  => $row['customs_reference'] ?? $this->customsReference,
            'vin' => $row['vin'],
            'npi' => $row['npi'],
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
