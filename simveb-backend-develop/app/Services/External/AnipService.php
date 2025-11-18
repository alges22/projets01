<?php

namespace App\Services\External;

use DOMXPath;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AnipService
{


    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['verify' => false]);
    }

    /*
    * get user informations from anip
    */

    public function getPerson(string $npi) : array
    {
        $wdsl = Str::replaceFirst('{npi}', $npi, file_get_contents(storage_path('wdsl/anip-person.xml')));

        $response = $this->httpClient->post(config('config.xroad_base_url'), [
            "body" => $wdsl,
            "headers" => [
                "Content-Type" => "text/xml;charset=utf-8",
                "SOAPAction" => "",
            ]
        ]);

        if ($response->getStatusCode() == Response::HTTP_OK) {
            //$xmlResponse = file_get_contents(storage_path('wdsl/anip-response.xml'));
            $xmlResponse = $response->getBody()->getContents();
            $doc = new DOMDocument();
            $doc->loadXML($xmlResponse);
            $xpath = new DOMXPath($doc);

            return [
                "npi" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/NPI")->item(0)?->nodeValue,
                "lastname" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/LASTNAME")->item(0)?->nodeValue,
                "firstname" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/FIRSTNAME")->item(0)?->nodeValue,
                "maritalname" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/MARITALNAME")->item(0)?->nodeValue,
                "birthdate" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/BIRTHDATE")->item(0)?->nodeValue,
                "birth_country_code" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/BIRTH_COUNTRY_CODE")->item(0)?->nodeValue,
                "birth_place" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/BIRTH_PLACE")->item(0)?->nodeValue,
                "residence_country_code" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/RESIDENCE_COUNTRY_CODE")->item(0)?->nodeValue,
                "residence_department" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/RESIDENCE_DEPARTMENT")->item(0)?->nodeValue,
                "residence_town" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/RESIDENCE_TOWN")->item(0)?->nodeValue,
                "residence_district" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/RESIDENCE_DISTRICT")->item(0)?->nodeValue,
                "residence_village" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/RESIDENCE_VILLAGE")->item(0)?->nodeValue,
                "residence_address" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/RESIDENCE_ADDRESS")->item(0)?->nodeValue,
                "phone_number" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/PHONE_NUMBER")->item(0)?->nodeValue,
                "email" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/EMAIL")->item(0)?->nodeValue,
                "nationality" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/NATIONALITY")->item(0)?->nodeValue,
                "sexe" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/SEXE")->item(0)?->nodeValue,
                "phone_number_indicatif" => $xpath->query("//ns:CITIZEN_BY_NPI_LITEResponse/PHONE_NUMBER_INDICATIF")->item(0)?->nodeValue,
            ];
        }else{
            Log::info('------------------ ANIP ERROR ---------------------------');
            Log::info($response->getStatusCode() . " | ".$response->getBody()->getContents());
            Log::info('------------------ ANIP ERROR ---------------------------');
        }

        return false;
    }
}
