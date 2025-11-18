<?php

namespace App\Traits;

use App\Models\Auction\AuctionSaleDeclaration;
use App\Models\Certificate;
use App\Models\GlassEngraving;
use App\Models\Reform\ReformDeclaration;
use App\Models\SaleDeclaration;
use App\Models\TintedWindowAuthorization;
use App\Models\Title\TitleDeposit;
use App\Models\Title\TitleRecovery;
use App\Services\GeneratePdfService;
use Illuminate\Support\Str;

trait HasCertificate
{
    public function certificate()
    {
        return $this->morphOne(Certificate::class, 'model');
    }

    /**
     * @param string $templateName
     * @return Certificate
     */
    public function createCertificate($templateName = null)
    {
        if ($this->certificate) {
            return $this->certificate;
        }

        $name = class_basename($this);
        $templateName = $templateName ?: strtolower(substr($name, 0, 1)) . substr($name, 1) . 'Certificate';

        $data = [
            'template_name' => $templateName,
            'model_id' => $this->id,
            'model_type' => get_class($this),
        ];

        switch ($this::class) {
            case SaleDeclaration::class:
            case TitleDeposit::class:
            case TitleRecovery::class:
                if (isset($this->demand->vehicleOwner->profile_id)) {
                    $data['profile_id'] = $this->demand->vehicleOwner->profile_id;
                } else {
                    $data['institution_id'] = $this->demand->vehicleOwner->institution_id;
                }
                break;

            case AuctionSaleDeclaration::class:
                if (isset($this->institution_id)) {
                    $data['institution_id'] = $this->institution_id;
                } else {
                    $data['profile_id'] = $this->auctioneer_id;
                }
                break;

            case ReformDeclaration::class:
                $data['profile_id'] = $this->declarant_id;
                break;

            case TintedWindowAuthorization::class:
                if (isset($this->vehicleOwner->profile_id)) {
                    $data['profile_id'] = $this->vehicleOwner->profile_id;
                } else {
                    $data['institution_id'] = $this->vehicleOwner->institution_id;
                }
                break;
            case GlassEngraving::class:
                if (isset($this->vehicleOwner->profile_id)) {
                    $data['profile_id'] = $this->vehicleOwner->profile_id;
                } else {
                    $data['institution_id'] = $this->vehicleOwner->institution_id;
                }
                break;
            default:
                break;
        }

        return Certificate::create($data);
    }

    /**
     * @return string
     */
    public function certificateView(): string
    {
        return 'certificates.' . Str::kebab(class_basename($this));
    }

    /**
     * @param bool $stream
     * @param ?string $view
     * @param array $data
     * @param ?string filename
     * @return mixed
     */
    public function generateCertificate(bool $stream = false, ?string $view = null, array $data = [], ?string $filename = null)
    {
        $certificate = $this->certificate;

        if (!$certificate) {
            $certificate = $this->createCertificate($view ?? null);
        }

        $filename = $filename ?:
            strtolower(Str::snake(class_basename($this)) . '_certificate_' . now()->timestamp . '_' . Str::random(3) . '.pdf');

        $data = array_merge($data, ['model' => $this->load($this::relations())]);

        $output = GeneratePdfService::generatePDF(
            view: $certificate->template_name,
            data: $data,
            filename: $filename,
            folder: 'certificates/' . Str::snake(class_basename($this)) . '_certificate',
            stream: $stream,
        );

        return $stream ? $output : asset('storage/' . Str::after($output, 'public/storage/'));
    }
}
