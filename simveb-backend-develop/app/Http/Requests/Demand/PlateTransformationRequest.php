<?php
namespace App\Http\Requests\Demand;

use App\Models\Config\Service;
use Illuminate\Foundation\Http\FormRequest;

class PlateTransformationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function storeRules(Service $service, FormRequest $request): array
    {
        return [
            'plate_color_id' => ['nullable', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['nullable', 'exists:plate_shapes,id'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(Service $service, FormRequest $request): array
    {
        return [
            'plate_color_id' => ['required', 'exists:plate_colors,id'],
            'front_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
            'back_plate_shape_id' => ['required', 'exists:plate_shapes,id'],
        ];
    }
}
