<?php

namespace App\Http\Requests\Auction;

use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class AddOfficialInAuctionSaleDeclarationRequest extends FormRequest
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
        $auctionSaleDeclaration = $this->auction_sale_declaration;

        return [
            'npi' => ['required', 'string', new ValideNpiRule, function ($attribute, $value, $fail) use ($auctionSaleDeclaration) {
                $officials = collect($auctionSaleDeclaration->officials);

                if (in_array($value, $officials->pluck('npi')->toArray())) {
                    $fail(__('Cet officiel est déjà dans la liste des officiels de cette déclaration.'));
                }
            }],
            'title' => ['required', 'string'],
        ];
    }
}
