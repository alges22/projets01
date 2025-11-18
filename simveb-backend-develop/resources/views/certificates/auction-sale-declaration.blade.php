@extends('pdf.layout')
@section('content')
    <main class="">
        <div class="mt-6 mb-8">
            <h3 class="font-semibold text-3xl text-center underline">CERTIFICAT DE VENTE AUX ENCHÈRES</h3>
        </div>
        <section class="mb-8">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Détail de la vente aux enchères</h4>
            </div>
            <div class="text-base px-4">
                <table class="table-fixed">
                    <tbody class="">
                        <tr class="border-b p-4">
                            <td class="">Référence de la déclaration</td>
                            <td class="font-semibold">{{ $model->reference }}</td>
                        </tr>
                        <tr class="border-b p-4">
                            <td class="">Commissaire Priseur</td>
                            <td class="font-semibold">{{ $model->auctioneer->identity->fullName }}</td>
                        </tr>
                        <tr class="border-b p-4">
                            <td class="">Date</td>
                            <td class="font-semibold">{{ $model->created_at }}</td>
                        </tr>
                        </tr>
                        <tr class="border-b p-4">
                            <td class="">Institution</td>
                            <td class="font-semibold">{{ $model->institution->name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-8">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Informations sur les vehicules</h4>
            </div>
            <div class="text-base px-4">
                <table class="table-fixed">
                    <thead class="font-semibold">
                        <tr class="border-b p-4">
                            <td></td>
                            <td>VIN</td>
                            <td>Acheteur</td>
                            <td>Prix</td>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($model->saledVehicles as $count => $saledVehicle)
                            <tr class="border-b p-4">
                                <td>{{ $count + 1 }}</td>
                                <td>{{ $saledVehicle->vehicle->vin }}</td>
                                <td>
                                    @if ($saledVehicle->buyerIdentity)
                                        {{ $saledVehicle->buyerIdentity->fullName }}
                                    @elseif ($saledVehicle->buyerInstitution)
                                        {{ $saledVehicle->buyerInstitution->socialReason }}
                                    @elseif ($saledVehicle->buyer_npi)
                                        {{ 'NPI: ' . $saledVehicle->buyer_npi }}
                                    @else
                                        {{ 'IFU: ' . $saledVehicle->buyer_ifu }}
                                    @endif
                                </td>
                                <td>{{ amountFormat($saledVehicle->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-8">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Liste des Officiels</h4>
            </div>
            <div class="text-base px-4">
                <table class="table-fixed">
                    <thead class="font-semibold">
                        <tr class="border-b p-4">
                            <td></td>
                            <td>NPI</td>
                            <td>Nom complet</td>
                            <td>Titre</td>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($model->OfficialIdentities as $count => $official)
                            <tr class="border-b p-4">
                                <td>{{ $count + 1 }}</td>
                                <td>{{ $official['npi'] }}</td>
                                <td>{{ $official['full_name'] }}</td>
                                <td>{{ $official['title'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-3">
            <div class="text-end">
                <img class="" src="{{ public_path($model->qr_code_path) }}">
            </div>
        </section>
    </main>
@endsection
