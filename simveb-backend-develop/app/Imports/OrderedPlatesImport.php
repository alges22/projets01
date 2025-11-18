<?php

namespace App\Imports;

use App\Models\Plate;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateOrder;
use App\Models\Plate\PlateShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OrderedPlatesImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function __construct(private readonly ?PlateOrder $plateOrder = null)
    {}

    public function rules(): array
    {
        return [
            'numero_serie' => ['required', 'unique:plates,serial_number'],
            'couleur' => ['required', 'string', 'exists:plate_colors,name'],
            'forme' => ['required', 'in:rec,car'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'numero_serie.required' => 'Le champ Numéro de série est obligatoire.',
            'numero_serie.unique' => 'La valeur du champ Numéro de série est déjà utilisée.',

            'couleur.required' => 'Le champ couleur est obligatoire.',
            'couleur.string' => 'Le champ couleur doit être une chaîne de caractères.',
            'couleur.exists' => 'La valeur champ couleur est invalide.',

            'forme.required' => 'Le champ forme est obligatoire.',
            'forme.string' => 'Le champ forme doit être une chaîne de caractères.',
            'forme.in' => 'La valeur du champ forme doit être rec ou car.',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ((isset($row['numero_serie']) && empty($row['numero_serie'])) || (isset($row[0]) && empty($row[0]))) {
            return null;
        }

        $data = [
            'serial_number' => $row['numero_serie'],
            'plate_shape_id' => PlateShape::whereRaw('LOWER(name) like ?', '%' . $row['forme'] . '%')->first()->id,
            'plate_color_id' => PlateColor::where('name', $row['couleur'])->first()->id,
            'anatt_order_id' => $this->plateOrder->id,
        ];

        $plate = Plate::create($data);

        return $plate;
    }

    public function headingRow(): int
    {
        return 2;
    }
}
