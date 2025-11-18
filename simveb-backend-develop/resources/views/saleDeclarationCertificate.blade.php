<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificat de cession</title>
    <style type="text/css">
        .header {
            padding: 10px;
            display: flex;
        }
        .header .left {
            width: 50%;
        }
        .header h1 {
            font-size: 40px;
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0;
            padding: 0;
        }
        .header .right {
            height: auto;
            align-self: flex-start;
            width: 50%;
            float: right;
        }
        img {
            max-width: 100%;
            margin: 0;
            padding: 0;
            float: right;
        }
        .container {
            padding: 30px;
            margin: 0px;
        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            padding: 0px;
            margin-top: 10px;
            background-color: rgb(132, 216, 216);
        }
        .label {
            font-weight: bold;
            float: left;
            width: 50%;
            padding: 5px;
        }
        .value {

            float: right;
            width: 50%;
            padding: 5px;
        }
        
    </style>
    <link rel="stylesheet" href="{{ public_path('assets/css/order-invoice.css') }}">
  
  </head>
  <body>
    <div>
        <div class="container">
            <div class="header">
                <div class="right">
                    <img src="{{public_path('assets/img/anatt.png')}}" style="max-width:60%;" alt="">
                </div>
                <div class="left">
                    <h1>Certificat de vente</h1>
                    <p>N° {{$model->reference}}</p>
                </div>
            </div>
            <div>
                <h2>Informations du vehicule</h2>
                <table class="w-full table-auto">
                    <tr class="border">
                        <td>
                            <div>
                                <div class="label">Pays d'origine du véhicule</div>
                                <div class="value">{{$model->vehicle->originCountry->name ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Numéro de chassis</div>
                                <div class="value">{{$model->vehicle->vin ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Marque</div>
                                <div class="value">{{$model->vehicle->brand?->name ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Modèle du véhicule</div>
                                <div class="value">{{$model->vehicle->vehicle_model ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Nombre de place assise</div>
                                <div class="value">{{$model->vehicle->number_of_seats ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Numéro de moteur</div>
                                <div class="value">{{$model->vehicle->engin_number ?? '-'}}</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="label">Poids total (en kg)</div>
                                <div class="value">{{$model->vehicle->charged_weight ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Poids à vide (en kg)</div>
                                <div class="value">{{$model->vehicle->empty_weight ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Parc d'achat du véhicule</div>
                                <div class="value">{{$model->vehicle->park?->name ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Année de première mise en circulation</div>
                                <div class="value">{{$model->vehicle->first_circulation_year ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Type de véhicule</div>
                                <div class="value">{{$model->vehicle->vehicleType?->label ?? '-'}}</div>
                            </div>
                        </td>
                    </tr>
                </table>
                <h2 >Informations du proprietaire</h2>
                @if ($owner['dataOriginType'] == 'Persons')
                <table class="w-full table-auto">
                    <tr class="border">
                        <td>
                            <div>
                                <div class="label">Nom</div>
                                <div class="value">{{$owner['firstname'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Prénom</div>
                                <div class="value">{{$owner['lastname'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Date de naissance</div>
                                <div class="value">{{$owner['birthdate'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Lieu de naissance</div>
                                <div class="value">{{$owner['birth_place'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Genre</div>
                                <div class="value">{{$owner['gender'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Pays de nationalité</div>
                                <div class="value">{{$owner['origin_country'] ?? '-'}}</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="label">Adresse</div>
                                <div class="value">{{$owner['address'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Téléphone</div>
                                <div class="value">{{$owner['telephone'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Email</div>
                                <div class="value">{{$owner['email'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">IFU</div>
                                <div class="value">{{$owner['ifu'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">NPI</div>
                                <div class="value">{{$owner['npi'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Référence BFU</div>
                                <div class="value">{{$owner['bfu'] ?? '-'}}</div>
                            </div>
                        </td>
                    </tr>
                </table>
                @else
                <table class="w-full table-auto">
                    <tr class="border">
                        <td>
                            <div>
                                <div class="label">RAISON SOCIALE</div>
                                <div class="value">{{$owner['name'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">IFU</div>
                                <div class="value">{{$owner['ifu'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Email</div>
                                <div class="value">{{$owner['email'] ?? '-'}}</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="label">Adresse</div>
                                <div class="value">{{$owner['address'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Téléphone</div>
                                <div class="value">{{$owner['telephone'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Référence BFU</div>
                                <div class="value">{{$owner['bfu'] ?? '-'}}</div>
                            </div>
                        </td>
                    </tr>
                </table>
                @endif
                <h2>Informations sur l'acheteur</h2>
                @if ($newOwner['dataOriginType'] == 'Persons')
                <table class="w-full table-auto">
                    <tr class="border">
                        <td>
                            <div>
                                <div class="label">Nom</div>
                                <div class="value">{{$newOwner['firstname'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Prénom</div>
                                <div class="value">{{$newOwner['lastname'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Date de naissance</div>
                                <div class="value">{{$newOwner['birthdate'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Lieu de naissance</div>
                                <div class="value">{{$newOwner['birth_place'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Genre</div>
                                <div class="value">{{$newOwner['gender'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Pays de nationalité</div>
                                <div class="value">{{$newOwner['origin_country'] ?? '-'}}</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="label">Adresse</div>
                                <div class="value">{{$newOwner['address'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Téléphone</div>
                                <div class="value">{{$newOwner['telephone'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Email</div>
                                <div class="value">{{$newOwner['email'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">IFU</div>
                                <div class="value">{{$newOwner['ifu'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">NPI</div>
                                <div class="value">{{$newOwner['npi'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Référence BFU</div>
                                <div class="value">{{$newOwner['bfu'] ?? '-'}}</div>
                            </div>
                        </td>
                    </tr>
                </table>
                @else
                <table class="w-full table-auto">
                    <tr class="border">
                        <td>
                            <div>
                                <div class="label">RAISON SOCIALE</div>
                                <div class="value">{{$newOwner['name'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">IFU</div>
                                <div class="value">{{$newOwner['ifu'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Email</div>
                                <div class="value">{{$newOwner['email'] ?? '-'}}</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="label">Adresse</div>
                                <div class="value">{{$newOwner['address'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Téléphone</div>
                                <div class="value">{{$newOwner['telephone'] ?? '-'}}</div>
                            </div>
                            <div>
                                <div class="label">Référence BFU</div>
                                <div class="value">{{$newOwner['bfu'] ?? '-'}}</div>
                            </div>
                        </td>
                    </tr>
                </table>
                @endif
            </div>
        </div>
    </div>
  </body>
</html>