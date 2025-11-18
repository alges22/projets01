<?php

namespace App\Http\Requests\Auction;

use Illuminate\Foundation\Http\FormRequest;

class RemoveOfficialFromAuctionSaleDeclarationRequest extends FormRequest
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
            'npi' => ['required', 'string', function ($attribute, $value, $fail) use ($auctionSaleDeclaration) {
                $officials = collect($auctionSaleDeclaration->officials);

                if (!in_array($value, $officials->pluck('npi')->toArray())) {
                    $fail(__("Cet officiel n'est pas dans la liste des officiels de cette déclaration."));
                }
            }],
        ];
    }
}
