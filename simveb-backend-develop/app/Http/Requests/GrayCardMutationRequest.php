<?php

namespace App\Http\Requests;

use App\Enums\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Vehicle\VehicleOwner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrayCardMutationRequest extends FormRequest
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
            'old_gray_card_id' => ['required','exists:gray_cards,id'],
            'old_vehicle_owner_id' => ['required','exists:vehicle_owners,id'],
            'gray_card_id' => ['required','exists:gray_cards,id'],
            'vehicle_owner_id' => ['required','exists:vehicle_owners,id'],
            'comment' => ['nullable', 'string'],
            'documents' => ['nullable', 'array'],
            'service_id' => ['required','exists:anatt_services,id'],
            'payment_status' => ['required',Rule::in(Status::toArray())],
            'mutation_certificate_number' => ['string','required'],
            'gray_card_number' => ['string','required'],
            'new_owner_npi' => ['string','required_without:new_owner_ifu', function ($attribute, $value, $fail) {VehicleOwner::where('npi', $value)->exists() || $fail('Aucune correspondance trouvée pour le NPI fourni.');},],
            'new_owner_ifu' => ['numeric','digits:13','required_without:new_owner_npi', function ($attribute, $value, $fail) {VehicleOwner::where('npi', $value)->exists() || $fail('Aucune correspondance trouvée pour l\'IFU fourni.');},],
        ];
    }
}

/*
$table->foreignUuid('mutattion_certificate_id');
$table->timestamp('paid_at')->nullable();
$table->timestamp('submitted_at')->nullable();*/
