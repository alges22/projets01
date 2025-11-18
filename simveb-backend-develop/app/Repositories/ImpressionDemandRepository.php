<?php

namespace App\Repositories;

use App\Consts\NotificationNames;
use App\Consts\Roles;
use App\Enums\Status;
use App\Models\ImpressionDemand;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Notifications\NotificationSender;
use App\Traits\UserDataTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImpressionDemandRepository
{
    use UserDataTrait;

    private $class;

    public function __construct()
    {
        $this->class = ImpressionDemand::class;
    }

    public function getAll(bool $paginate = true, $relations = [])
    {
        $query = $this->class::filter()->with($relations)->orderByDesc('created_at');
        $user = auth()->user();

        if ($user->hasRole(Roles::SPACE_MEMBER)) {

            return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
        } elseif ($user->hasRole(Roles::SPACE_ADMIN) || $user->hasRole(Roles::SPACE_HEADER)) {

            return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
        } elseif ($user->hasRole(Roles::ADMIN)) {

            return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
        }
        return [];
    }

    public function initDemand(array $data)
    {
        $immatriculationDemand = Demand::where('reference', $data['reference'])->first();

        if ($immatriculationDemand->status != Status::print_order_emitted->name) {
            return  ['false', ['message' => "Impossible de créer une demande d'impression sur cette demande d'immatriculation. Raison: niveau de validation invalide."]];
        }

        if ($immatriculationDemand->activeTreatment->assigned_to_space_at) {
            return [false, ['message' => "Cette demande a déjà été affectée à un imprimeur."]];
        }

        if (ImpressionDemand::where(['demand_id' => $immatriculationDemand->id, 'demand_type' => Demand::class])->whereIn('status', [Status::pending->name, Status::validated->name])->exists()) {
            return [false, ['message' => "Une demande d'impression a déjà été faite sur cette demande d'immatriculation."]];
        }

        try {
            DB::beginTransaction();

            $otp = $immatriculationDemand->otpCodes()->create([
                'value' => rand(1000, 9999),
                'expired_at' => Carbon::now()->addMinutes(10),
            ]);

            sendMail(
                null,
                $immatriculationDemand->vehicleOwner->identity,
                NotificationNames::IMPRESSION_OTP_SENT,                       
                ['otp' => $otp->value]
            );

            DB::commit();

            return [true, ['message' => "Code OTP envoyé avec succès"]];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function store(array $data)
    {
        $immatriculationDemand = Demand::where('reference', $data['reference'])->first();

        if ($immatriculationDemand->status != Status::print_order_emitted->name) {
            return  ['false', ['message' => "Impossible de créer une demande d'impression sur cette demande d'immatriculation. Raison: niveau de validation invalide."]];
        }

        if ($immatriculationDemand->activeTreatment->assigned_to_space_at) {
            return [false, ['message' => "Cette demande a déjà été affectée à un imprimeur."]];
        }

        if (ImpressionDemand::where(['demand_id' => $immatriculationDemand->id, 'demand_type' => Demand::class])->whereIn('status', [Status::pending->name, Status::validated->name])->exists()) {
            return [false, ['message' => "Une demande d'impression a déjà été faite sur cette demande d'immatriculation."]];
        }

        $otp = $immatriculationDemand->otpCodes()->where('value', $data['otp_code'])->first();
        if (!($otp && now() < $otp->expired_at)) {
            return [false, ['message' => !$otp ? "Otp invalide" : "Otp expiré"]];
        }

        try {
            DB::beginTransaction();
            $otp = $immatriculationDemand->otpCodes()->delete();

            $user = auth()->user();
            $spaceId = null;

            $immatriculationDemand->activeTreatment->update([
                'print_by_space_id' => $spaceId,
                'assigned_to_space_at' => now(),
            ]);

            $demand = $this->class::create([
                'demand_id' => $immatriculationDemand->id,
                'demand_type' => Demand::class,
                'space_id' => $spaceId,
                'created_by' => $user->id,
            ]);

            DB::commit();
            return [true, $demand->load($this->class::relations())];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function validationCreate($impressionDemand)
    {
        //TOD: manage other demand case
        $immatriculationDemand = $impressionDemand->demand;

        $nbRequiredPlate = $immatriculationDemand->back_plate_shape_id ? 2 : 1;

        $plateQuery = Plate::where('space_id', $impressionDemand->space_id)
            ->where('plate_color_id', $immatriculationDemand->plate_color_id)
            ->where('in_space_stock', true)
            ->with(['plateShape:id,name,description,cost', 'plateColor:id,name,label,color_code,text_color,cost']);

        $fronts = (clone $plateQuery)->where('plate_shape_id', $immatriculationDemand->front_plate_shape_id)->get();
        $backs = $nbRequiredPlate == 2 ? $plateQuery->where('plate_shape_id', $immatriculationDemand->back_plate_shape_id)->get() : [];

        return [
            'nb_required_plate' => $nbRequiredPlate,
            'fronts' => $fronts,
            'backs' => $backs,
        ];
    }

    public function validateDemand(array $data)
    {
        $impressionDemand = $this->class::findOrFail($data['impression_demand_id']);

        if ($impressionDemand->status != Status::pending->name) {
            return [false, ['message' => "Cette demande est déjà traitée."]];
        }

        try {
            DB::beginTransaction();

            $frontPlate = Plate::findOrFail($data['front_plate_id']);
            $backPlate = null;

            if ($frontPlate->plate_color_id != $impressionDemand->demand->plate_color_id || $frontPlate->plate_shape_id != $impressionDemand->demand->front_plate_shape_id) {
                return [false, ['message' => "La plaque avant sélectionnée n'a pas la couleur et/ou la forme requise."]];
            }

            if ($backPlate && ($backPlate->plate_color_id != $impressionDemand->demand->plate_color_id || $backPlate->plate_shape_id != $impressionDemand->demand->back_plate_shape_id)) {
                return [false, ['message' => "La plaque arrière sélectionnée n'a pas la couleur et/ou la forme requise."]];
            }

            $frontPlate->update(['in_space_stock' => false]);

            if (isset($data['back_plate_id'])) {
                $backPlate = Plate::findOrFail($data['back_plate_id']);
                $backPlate->update(['in_space_stock' => false]);
            }

            $impressionDemand->update([
                'status' => Status::validated->name,
                'validated_at' => now(),
                'validated_by' => $this->user()->id,
                'front_plate_id' => $frontPlate->id,
                'back_plate_id' => $backPlate?->id,
            ]);

            $impressionDemand->demand->update([
                'status' => Status::printing_in_progress->name,
                'front_plate_id' => $frontPlate->id,
                'back_plate_id' => $backPlate?->id,
            ]);

            DB::commit();

            return [true, $impressionDemand->load($this->class::relations())];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function rejectDemand(array $data)
    {
        $demand = $this->class::findOrFail($data['impression_demand_id']);

        if ($demand->status != Status::pending->name) {
            return [false, ['message' => "Cette demande est déjà traitée."]];
        }

        $demand->update([
            'status' => Status::rejected->name,
            'rejected_at' => now(),
            'rejected_by' => $this->user()->id,
            'rejected_reason' => $data['reason'],
        ]);

        return [true, $demand];
    }

    public function confirmDemand(array $data)
    {
        $impressionDemand = ImpressionDemand::findOrFail($data['impression_demand_id']);

        if ($impressionDemand->status != Status::validated->name) {
            return [false, ['message' => "Cette demande d'impression n'a pas été validée."]];
        } elseif ($impressionDemand->status != Status::validated->name) {
            return [false, ['message' => "Cette demande d'impression a déjà été confirmée."]];
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();

            $impressionDemand->update([
                'status' => Status::confirmed->name,
                'confirmed_at' => now(),
                'confirmed_by_id' => $user->id,
            ]);

            $impressionDemand->demand->update([
                'status' => Status::printed->name,
            ]);

            $impressionDemand->demand->activeTreatment->update([
                'printed_by' => $user->id,
                'printed_at' => now(),
                'print_observations' => isset($data['observations']) ? $data['observations'] : '',
            ]);

            sendMail(
                null,
                $impressionDemand->demand->vehicleOwner->identity,
                NotificationNames::IMPRESSION_FINISHED,                       
                ['reference' => $impressionDemand->demand->reference]
            );
            DB::commit();

            return [true, $impressionDemand->load($this->class::relations())];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function confirmPlateReception(ImpressionDemand $impressionDemand)
    {
        if ($impressionDemand->status != Status::confirmed->name) {
            return [false, "Impossible d'effectuer cette action sur cette impression."];
        }

        try {
            DB::beginTransaction();

            $impressionDemand->update([
                'given_to_applicant_at' => now(),
                'status' => Status::given->name,
            ]);

            $impressionDemand->demand->update([
                'status' => Status::given_to_applicant->name,
            ]);

            $impressionDemand->demand->activeTreatment->update([
                'given_to_applicant_at' => now(),
            ]);

            // send notification to anatt member
            /* sendMail(
                null,
                $impressionDemand->demand->vehicleOwner->identity,
                NotificationNames::PLATE_DELIVERED,                       
                ['reference' => $impressionDemand->demand->reference]
            ); */
            DB::commit();

            return [true, $impressionDemand->load($this->class::relations())];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }
}
