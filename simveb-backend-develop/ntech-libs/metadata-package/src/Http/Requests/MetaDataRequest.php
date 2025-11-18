<?php
namespace Ntech\MetadataPackage\Http\Requests;

use Ntech\MetadataPackage\Enums\MetaDataKeys;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetaDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "metadata" => ["array","required"],
            "metadata.*.name" => [Rule::in(MetaDataKeys::toArray())]
        ];
    }
}
