<?php

namespace App\Http\Requests\Demand;

use App\Enums\Status;
use App\Models\Order\Demand;
use Illuminate\Foundation\Http\FormRequest;

class CloseDemandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'demand_id' => ['bail', 'required', 'exists:demands,id', function ($attribute, $value, $fail) {
                // $demand = Demand::find($value);

                // if ($demand->status != Status::print_order_validated->name) {
                //     $fail('Vous ne pouvez pas effectué cette action sur cette demande.');
                // }
            }],
        ];
    }
}
