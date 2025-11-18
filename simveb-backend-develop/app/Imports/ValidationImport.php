<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ValidationImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function __construct(private readonly array $rules = [], private readonly array $customValidationMessages = [], private readonly int $headRow = 1)
    {}

   /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return null;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function customValidationMessages()
    {
        return $this->customValidationMessages;
    }

    public function headingRow(): int
    {
        return $this->headRow;
    }
}
