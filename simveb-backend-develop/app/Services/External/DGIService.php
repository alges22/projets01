<?php

namespace App\Services\External;

use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DGIService
{

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['verify' => false]);
    }

    public function getCompanyByIFU(string $ifu)
    {
        $response = $this->httpClient->get(config('config.xroad_base_url')."/restapi?ifu=$ifu",[
            'headers' => [
                    'Authorization' => 'Bearer ' . config('config.dgi_token'),
                    'Uxp-Client' => 'BJ/GOV/ANATT/SIMVEB',
                    'Uxp-Service' => 'BJ/GOV/DGI/CFISC/DETAIL-IFU/V1',
                    'Accept' => 'application/json',
                ],
            ]);

            $jsonResponse = json_decode($response->getBody()->getContents(), true);
            if($response->getStatusCode() === Response::HTTP_OK && json_last_error() == JSON_ERROR_NONE){
                return $jsonResponse;
            }else{
                Log::info('------------------ DGI ERROR ---------------------------');
                Log::info($response->getStatusCode() . " | ".$response->getBody()->getContents());
                Log::info('------------------ DGI ERROR ---------------------------');
            }

            return false;
    }

}
