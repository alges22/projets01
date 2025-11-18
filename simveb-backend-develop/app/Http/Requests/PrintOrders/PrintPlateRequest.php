<?php

namespace App\Http\Requests\PrintOrders;

use App\Consts\AvailableServiceType;
use App\Enums\PrintOrderTypesEnum;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\Treatment\PrintOrder;
use Illuminate\Foundation\Http\FormRequest;

class PrintPlateRequest extends FormRequest
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
        $printOrder = PrintOrder::find($this->print_order_id);
        $request = $this;

        $rules['print_order_id'] = ['bail', 'required', 'exists:print_orders,id', function ($attribute, $value, $fail) use ($printOrder) {
            // if (
            //     $printOrder->type == PrintOrderTypesEnum::gray_card->name ||
            //     in_array($printOrder->type, [PrintOrderTypesEnum::plate->name, PrintOrderTypesEnum::both->name]) && $printOrder->status != Status::active->name
            // ) {
            //     $fail('Impossible de faire cette action sur ce dossier.');
            // }
        }];

        if ($printOrder) {
            $plateSidesToPrint = $this->getPlateSidesToPrint($printOrder?->demand);
            $onlineProfile = getOnlineProfile();

            $rules = array_merge(
                $rules,
                [
                    'front_plate_rfid' => ['nullable', 'string', 'unique:plates,rfid'],
                    'back_plate_rfid' => ['nullable', 'string', 'unique:plates,rfid', 'different:front_plate_rfid'],
                    'images' => ['bail', 'required', 'array', 'min:1'],
                    'images.*' => ['bail', 'required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240'],
                ]
            );

            if (in_array('front', $plateSidesToPrint['sides'] ?? [])) {
                $rules = array_merge(
                    $rules,
                    [
                        'front_plate' => ['bail', 'required', 'exists:plates,serial_number', function ($attribute, $value, $fail) use ($plateSidesToPrint, $onlineProfile) {
                            $plate = Plate::where('serial_number', $value)->first();
                            if ($plate) {
                                if ($plate->plate_color_id != $plateSidesToPrint['plate_color']->id) {
                                    $fail('Veuillez choisir une plaque de couleur ' . $plateSidesToPrint['plate_color']->label);
                                }

                                if ($plate->plate_shape_id != $plateSidesToPrint['front_plate_shape']->id) {
                                    $fail('Veuillez choisir une plaque de forme ' . $plateSidesToPrint['front_plate_shape']->name);
                                }

                                if ($onlineProfile->type->code == ProfileTypesEnum::anatt->name && !$plate->in_anatt_stock || $onlineProfile->type->code != ProfileTypesEnum::anatt->name && (!$plate->in_affiliate_stock || $onlineProfile->institution->id != $plate->institution_id)) {
                                    $fail('Cette plaque n\'est pas ou plus dans votre stock');
                                }
                            }
                        }]
                    ]
                );
            } else {
                $rules = array_merge($rules, ['front_plate' => 'prohibited']);
            }

            if (in_array('back', $plateSidesToPrint['sides'] ?? [])) {
                $rules = array_merge(
                    $rules,
                    [
                        'back_plate' => ['bail', 'required', 'exists:plates,serial_number', function ($attribute, $value, $fail) use ($request, $plateSidesToPrint, $onlineProfile) {
                            if ($value == $request->front_plate) {
                                $fail('La plaque arrière ne peut pas être la même que la plaque avant');
                            }

                            $plate = Plate::where('serial_number', $value)->first();
                            if ($plate) {
                                if ($plate->plate_color_id != $plateSidesToPrint['plate_color']->id) {
                                    $fail('Veuillez choisir une plaque de couleur ' . $plateSidesToPrint['plate_color']->label);
                                }

                                if ($plate->plate_shape_id != $plateSidesToPrint['back_plate_shape']->id) {
                                    $fail('Veuillez choisir une plaque de forme ' . $plateSidesToPrint['back_plate_shape']->name);
                                }

                                if ($onlineProfile->type->code == ProfileTypesEnum::anatt->name && !$plate->in_anatt_stock || $onlineProfile->type->code != ProfileTypesEnum::anatt->name && (!$plate->in_affiliate_stock || $onlineProfile->institution->id != $plate->institution_id)) {
                                    $fail('Cette plaque n\'est pas ou plus dans votre stock');
                                }
                            }
                        }]
                    ]
                );
            } else {
                $rules = array_merge($rules, ['back_plate' => 'prohibited']);
            }
        }

        return $rules;
    }

    private function getPlateSidesToPrint(Demand $demand = null): array
    {
        if (empty($demand)) {
            return [];
        }

        $demandModel = $demand->model;

        $serviceTypeCode = $demand->service->type->code;

        return match ($serviceTypeCode) {
            AvailableServiceType::IMMATRICULATION_STANDARD,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
            AvailableServiceType::RE_IMMATRICULATION,
            AvailableServiceType::PLATE_TRANSFORMATION => [
                'sides' => in_array($serviceTypeCode, [AvailableServiceType::IMMATRICULATION_STANDARD, AvailableServiceType::RE_IMMATRICULATION, AvailableServiceType::PLATE_TRANSFORMATION])
                    ? ($demandModel->vehicle->category->nb_plate == 2 ? ['front', 'back'] : ['back'])
                    : ($demandModel->immatriculation->vehicle->category->nb_plate == 2 ? ['front', 'back'] : ['back']),
                'front_plate_shape' => $demandModel->frontPlateShape,
                'back_plate_shape' => $demandModel->backPlateShape,
                'plate_color' => $demandModel->plateColor,
            ],
            AvailableServiceType::PLATE_DUPLICATE => function ($demandModel) {
                $oldPlate = Plate::find($demandModel->old_plate_id);
                if ($oldPlate->id == $oldPlate->immatriculation->vehicle->front_plate_id) {
                    return [
                        'sides' => ['front'],
                        'front_plate_shape' => $oldPlate->plateShape,
                        'plate_color' => $oldPlate->plateColor,
                    ];
                } else {
                    return [
                        'sides' => ['back'],
                        'back_plate_shape' => $oldPlate->plateShape,
                        'plate_color' => $oldPlate->plateColor,
                    ];
                }
            },
            default => [],
        };
    }
}
