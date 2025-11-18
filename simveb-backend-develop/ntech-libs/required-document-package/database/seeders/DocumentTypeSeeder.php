<?php

namespace Ntech\RequiredDocumentPackage\Database\Seeders;

use Illuminate\Database\Seeder;
use Ntech\RequiredDocumentPackage\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'cni' => "Carte nationale d'identité",
            'passport' => "Passport",
            'driver_licence' => "Permis de conduire",
            'rccm' => "Registre du Commerce et de Crédit Mobilier",
            'vehicle_photo' => 'Photo du véhicule',
            'bfu' => 'BFU',
            'declaration_of_law' => 'Déclaration de droit',
        ];

        foreach ($types as $type => $desc) {
            $data = [
                'code' => $type,
                'description' => $desc,
            ];

            $documentType = DocumentType::updateOrCreate([
                'code' => $type,
            ], $data);

        }
    }
}
