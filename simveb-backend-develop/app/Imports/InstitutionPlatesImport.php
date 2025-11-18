<?php

namespace App\Imports;

use App\Models\Plate;
use App\Models\Plate\PlateOrder;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InstitutionPlatesImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function __construct(private readonly ?PlateOrder $plateOrder = null)
    {
    }

    public function rules(): array
    {
        return [
            'numero_serie' => ['bail', 'required', 'exists:plates,serial_number', function ($attribute, $value, $fail) {
                if ($value) {
                    $plate = Plate::where('serial_number', $value)->first();
                    if ($plate && !$plate->in_anatt_stock) {
                        $fail('Cette plaque appartient déjà à une institution.');
                    }
                }
            }],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'numero_serie.required' => 'Le champ Numéro de série est obligatoire.',
            'numero_serie.exists' => 'La valeur du champ Numéro de série est invalide.',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $plate = Plate::where('serial_number', $row['numero_serie'])->first();

        if ($plate) {
            $plate->update([
                'institution_id' => $this->plateOrder->buyer_id,
                'institution_order_id' => $this->plateOrder->id,
            ]);
        }

        return $plate;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
