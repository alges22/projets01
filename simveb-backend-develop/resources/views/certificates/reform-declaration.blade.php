@extends('pdf.layout')
@section('content')
    <main class="">
        <div class="mt-6 mb-8">
            <h3 class="font-semibold text-3xl text-center underline">CERTIFICAT DE REFORME</h3>
        </div>
        <section class="mb-8">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Détail de la réforme</h4>
            </div>
            <div class="text-base px-4">
                <table class="table-fixed">
                    <tbody class="">
                        <tr class="border-b p-4">
                            <td class="">Référence de la réforme</td>
                            <td class="font-semibold">{{ $model->reference }}</td>
                        </tr>
                        <tr class="border-b p-4">
                            <td class="">Déclarant</td>
                            <td class="font-semibold">{{ $model->declarant?->identity->fullName }}</td>
                        </tr>
                        <tr class="border-b p-4">
                            <td class="">Date</td>
                            <td class="font-semibold">{{ $model->created_at }}</td>
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
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($model->reformedVehicles as $count => $reformedVehicle)
                            <tr class="border-b p-4">
                                <td>{{ $count + 1 }}</td>
                                <td>{{ $reformedVehicle->vehicle->vin }}</td>
                                <td>
                                    @if ($reformedVehicle->buyerIdentity)
                                        {{ $reformedVehicle->buyerIdentity->fullName }}
                                    @elseif ($reformedVehicle->buyerInstitution)
                                        {{ $reformedVehicle->buyerInstitution->socialReason }}
                                    @elseif ($reformedVehicle->buyer_npi)
                                        {{ 'NPI: ' . $reformedVehicle->buyer_npi }}
                                    @else
                                        {{ 'IFU: ' . $reformedVehicle->buyer_ifu }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
