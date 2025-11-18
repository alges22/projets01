<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificateCollection;
use App\Models\Certificate;
use App\Services\CertificateService;

class CertificateController extends Controller
{

    public function __construct(private readonly CertificateService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(new CertificateCollection($this->service->getOnlineCertificate()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        return $this->service->generateCertificate($certificate);
    }
}
