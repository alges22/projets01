<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CertificateService
{

    public function generateCertificate(Certificate $certificate)
    {
        try {
            return $certificate->model->generateCertificate();
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('Une erreure s\'est produite'));
        }
    }

    public function getOnlineCertificate()
    {
        $profile = getOnlineProfile();

        return Certificate::query()
            ->when($profile->isUserProfile(), fn($query) => $query->where('profile_id', $profile->id))
            ->when(!$profile->isUserProfile(), fn($query) => $query->where('institution_id', $profile->institution_id))
            ->orderBy('certificates.created_at', 'desc')
            ->where('model_type', 'App\Models\SaleDeclaration')
            ->filter()
            ->get();
    }
}
