<?php

namespace Ntech\RequiredDocumentPackage\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ntech\RequiredDocumentPackage\Models\RequiredDocumentType;

class RequiredDocumentTypeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $relationType = $this->relation_type;
        $documentTypeId = $this->document_type_id;
        $ignoreId = $this->required_document_type?->id;

        return [
            'relation_type' => [
                'required', 'string', 'in:' . implode(',', RequiredDocumentType::types()),
                Rule::unique('required_document_types')->when(
                    $relationType && $documentTypeId,
                    function ($q) use ($relationType, $documentTypeId, $ignoreId) {
                        return $q->where('relation_type', $relationType)
                            ->where('document_type_id', $documentTypeId)
                            ->when($ignoreId, function ($query) use ($ignoreId) {
                                $query->whereNot('id', $ignoreId);
                            });
                    }
                ),
            ],
            'document_type_id' => ['required', 'exists:document_types,id'],
            'required' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'relation_type.unique' => "La valeur combinée des champs type de relation et type de document est déjà utilisée."
        ];
    }
}
