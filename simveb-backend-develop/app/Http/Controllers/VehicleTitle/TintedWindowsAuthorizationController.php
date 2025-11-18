<?php

namespace App\Http\Controllers\VehicleTitle;

use App\Http\Controllers\Controller;
use App\Http\Requests\TintedWindowsAuthorizationRequest;
use App\Models\Account\User;
use App\Models\SimvebFile;
use App\Models\TintedWindowAuthorization;
use App\Notifications\SimvebNotification;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TintedWindowsAuthorizationController extends Controller
{
    use CrudRepositoryTrait, UploadFile;

    public function __construct()
    {
        $this->initRepository(TintedWindowAuthorization::class);
        $this->authorizeResource(TintedWindowAuthorization::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TintedWindowsAuthorizationRequest $request)
    {
        try {
            DB::beginTransaction();

            $tintedWindowsAuthorization = TintedWindowAuthorization::create([
                'vehicule_id' => $request->validated('vehicule_id'),
                'user_id' => $request->validated('user_id'),
                'under_review' => null,
                'approved' => null,
                'paid' => null,
                'ongoing_issuance' => null,
                'issued' => null,
                'expired' => null,
                'reviewed_at' => null,
                'approved_at' => null,
                'paid_at' => null,
                'issued_at' => null,
                'expired_at' => null,
                'validity_period_in_months' => $request->validated('validity_period_in_months'),
            ]);

            $documents = Arr::pull($request->validated(), 'documents');

            if ($documents) {
                foreach($documents as $document) {
                    $fileInfo = $this->saveFile($document['file'], "tinted_windows_authorization");

                    $tintedWindowsAuthorization->files()->create([
                        'path' => $fileInfo,
                        'type' => SimvebFile::FILE,
                        'file_type_id' => $document['type_id'],
                    ]);
                }
            }

            DB::commit();

            if ($request->has('user_id')) {
                $client = User::where('user_id', $request->validated('user_id'))->first();
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-application-submitted-successfully', true, true));
            }

            return $tintedWindowsAuthorization->load(TintedWindowAuthorization::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TintedWindowAuthorization $tintedWindowAuthorization)
    {
        return response($tintedWindowAuthorization->load($tintedWindowAuthorization::relations()));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TintedWindowAuthorization $tintedWindowAuthorization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TintedWindowsAuthorizationRequest $request, TintedWindowAuthorization $tintedWindowAuthorization)
    {
        $reviewed_at = $request->validated('reviewed_at');
        if ($tintedWindowAuthorization->under_review === null && $request->validated('under_review') === true) {
            $reviewed_at = Carbon::now();
        }

        $approved_at = $request->validated('approved_at');
        if ($tintedWindowAuthorization->approved === null && $request->validated('approved') === true) {
            $approved_at = Carbon::now();
        }

        $paid_at = $request->validated('paid_at');
        if ($tintedWindowAuthorization->paid === null && $request->validated('paid') === true) {
            $paid_at = Carbon::now();
        }

        $issued_at = $request->validated('issued_at');
        $expired = $request->validated('expired');
        $expired_at = $request->validated('expired_at');
        if ($tintedWindowAuthorization->issued === null && $request->validated('issued') === true) {
            $issued_at = Carbon::now();
            $expired_at = null;
            $expired = null;
        }

        $response = response($this->repository->update($tintedWindowAuthorization, [
            'vehicule_id' => $request->validated('vehicule_id'),
            'user_id' => $request->validated('user_id'),
            'under_review' => $request->validated('under_review'),
            'approved' => $request->validated('approved'),
            'paid' => $request->validated('paid'),
            'ongoing_issuance' => $request->validated('ongoing_issuance'),
            'issued' => $request->validated('issued'),
            'expired' => $expired,
            'reviewed_at' => $reviewed_at,
            'approved_at' => $approved_at,
            'paid_at' => $paid_at,
            'issued_at' => $issued_at,
            'expired_at' => $expired_at,
            'validity_period_in_months' => $request->validated('validity_period_in_months'),
        ]));

        if ($response->status() === 200 && $request->has('user_id')) {
            $client = User::where('user_id', $request->validated('user_id'))->first();

            if ($tintedWindowAuthorization->under_review === null && $request->validated('under_review') === true) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-application-under-review', true, true));
            }

            if ($tintedWindowAuthorization->approved === null && $request->validated('approved') === true) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-approved', true, true));
            } elseif ($tintedWindowAuthorization->approved === null && $request->validated('approved') === false) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-rejected', true, true));
            }

            if ($tintedWindowAuthorization->paid === null && $request->validated('paid') === true) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-payment-successful', true, true));
            } elseif ($tintedWindowAuthorization->paid === null && $request->validated('paid') === false) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-payment-error', true, true));
            }

            if ($tintedWindowAuthorization->ongoing_issuance === null && $request->validated('ongoing_issuance') === true) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-ongoing', true, true));
            }

            if ($tintedWindowAuthorization->issued === null && $request->validated('issued') === true) {
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-issuance-successful', true, true));
            }
        }

        return $response;
    }

    public function updateExpiryStatus(TintedWindowsAuthorizationRequest $request, TintedWindowAuthorization $tintedWindowAuthorization)
    {
        $issuanceDate = Carbon::parse($request->validated('issued_at'));

        if ($issuanceDate->addMonths((int) $request->validated('validity_period_in_months'))->gt(Carbon::now())) {
            $response = response($this->repository->update($tintedWindowAuthorization, [
                'vehicule_id' => $request->validated('vehicule_id'),
                'user_id' => $request->validated('user_id'),
                'under_review' => null,
                'approved' => null,
                'paid' => null,
                'ongoing_issuance' => null,
                'issued' => null,
                'expired' => true,
                'reviewed_at' => null,
                'approved_at' => null,
                'paid_at' => null,
                'issued_at' => null,
                'expired_at' => Carbon::now(),
                'validity_period_in_months' => $request->validated('validity_period_in_months'),
            ]));

            if ($response->status() === 200 && $request->has('user_id')) {
                $client = User::where('user_id', $request->validated('user_id'))->first();
                $client->notify(new SimvebNotification('tinted-windows-authorization-card-expired', true, true));
            }

            return $response;
        }

        return Response('Tinted windows authorization card not expired yet');
    }

    public function getExpiryStatus(TintedWindowsAuthorizationRequest $request)
    {
        return Response(TintedWindowAuthorization::where('vehicle_id', $request->vehicle_id)->get()->first());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TintedWindowAuthorization $tintedWindowAuthorization)
    {
        return response($this->repository->destroy($tintedWindowAuthorization));
    }
}
