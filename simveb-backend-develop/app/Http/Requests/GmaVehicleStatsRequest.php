<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class GmaVehicleStatsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return getOnlineProfile()->hasPermissionTo('view-stats-' . Str::kebab('GmaVehicle'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'statistics_filter' => ['array'],
            'statistics_filter.*.type' => ['required', 'string', 'in:manual,week,month,year'],
            'statistics_filter.*.start_date' => ['required_if:statistics_filter.*.type,manual', 'string'],
            'statistics_filter.*.end_date' => ['required_if:statistics_filter.*.type,manual', 'string'],
            'general_view_filter' => ['array'],
            'general_view_filter.*.type' => ['required', 'string', 'in:manual,week,month,year'],
            'general_view_filter.*.start_date' => ['required_if:statistics_filter.*.type,manual', 'string'],
            'general_view_filter.*.end_date' => ['required_if:statistics_filter.*.type,manual', 'string'],
        ];
    }
}
