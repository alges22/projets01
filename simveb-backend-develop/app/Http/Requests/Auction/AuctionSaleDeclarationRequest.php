<?php

namespace App\Http\Requests\Auction;

use App\Rules\Demands\ValideIfuRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class AuctionSaleDeclarationRequest extends FormRequest
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
        $method = $this->input('_method');
        $auctionSaleDeclaration = $this->auction_sale_declaration;

        $rules = [
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'officials.*.npi' => ['required', new ValideNpiRule],
            'officials.*.title' => ['required', 'string'],
            'vehicles.*.vehicle_vin' => ['required', 'exists:vehicles,vin'],
            'vehicles.*.buyer_npi' => ['required_without:vehicles.*.buyer_ifu', new ValideNpiRule],
            'vehicles.*.buyer_ifu' => ['required_without:vehicles.*.buyer_npi', new ValideIfuRule],
            'vehicles.*.price' => ['required', 'numeric', 'min:1'],
        ];

        if ($method && strtolower($method) == 'put') {
            $rules['report'] = ['nullable', 'file'];
            $rules['officials'] = ['nullable', 'array', 'min:0'];
            $rules['vehicles'] = ['nullable', 'array', 'min:0'];
        } else {
            $rules['report'] = ['required', 'file'];
            $rules['officials'] = ['required', 'array', 'min:1'];
            $rules['vehicles'] = ['required', 'array', 'min:1'];
        }

        if ($auctionSaleDeclaration) {
            $this->merge(['auction_sale_declaration_id' => $auctionSaleDeclaration->id]);

            $rules['auction_sale_declaration_id'] = ['nullable', function ($attribute, $value, $fail) use ($auctionSaleDeclaration) {
                if ($auctionSaleDeclaration->reformDeclarations()->count() > 0) {
                    $fail('Impossible de modifier une déclaration de vente aux enchères ayant une/des réforme(s).');
                }
            }];
        }

        return $rules;
    }
}
