@extends('pdf.layout')
@section('content')
    <main class="" style="font-size: 20px;">
        <div class="">
            <h1 class="text-center" style="font-size: xx-large">GRAVAGE DES VITRES</h1>
        </div>
        <section class="mb-8">
            <div class="px-4">
                <table class="table-fixed">
                    <tbody class="">
                    <tr class="p-4">
                        <td class="">Numéro d'immatriculation:</td>
                        <td class="font-semibold">
                            {{ $model->vehicle->immatriculation->number_label }}</td>
                    </tr>
                    <tr class="p-4">
                        <td class="">Numéro du châssis:</td>
                        <td class="font-semibold">{{ $model->vehicle->vin }}</td>
                    </tr>
                    <tr class="p-4">
                        <td class="">Marque:</td>
                        <td class="font-semibold">{{ $model->vehicle->brand->name }}</td>
                    </tr>
                    <tr class="p-4">
                        <td class="">Modèle de véhicule:</td>
                        <td class="font-semibold">{{ $model->vehicle->vehicle_model }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <div class="">
                <div class="">
                    <h3>{{ $model->vehicleOwner->identity->fullName }}</h3>
                </div>
                <div class="">
                    <p>L'Agence Nationale des Transports Terrestres (ANaTT) vous informe que votre demande du
                        {{ $model->created_at->format('d/m/Y à H:i') }} pour le gravage des vitres de votre véhicule immatriculé <span
                            class="font-semibold">{{ $model->vehicle->immatriculation->number_label }}</span>
                        a été acceptée.</p>
                    <p>Le gravage du numéro d'immatriculation sur les vitres est une mesure de sécurité permettant d'identifier facilement votre
                        véhicule en cas de vol. Il consiste à inscrire un numéro unique sur les vitres du véhicule.</p>
                    <p>Nous vous rappelons qu'il est essentiel que le gravage soit effectué par un professionnel agréé
                        afin de garantir sa conformité avec la réglementation en vigueur.</p>
                    <p>Pour toute information complémentaire, n'hésitez pas à nous contacter.</p>
                </div>
            </div>
        </section>

        <section>
            <div class="">
                <h5>Fait le {{ $model->certificate->created_at->format('d/m/Y à H:i') }}</h5>
            </div>
        </section>
    </main>
@endsection
