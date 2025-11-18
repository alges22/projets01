<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExportErrorFile implements FromCollection
{
    public function __construct(private readonly array $errors)
    {}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->errors);
    }
}
