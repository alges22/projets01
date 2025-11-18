@extends('pdf.layout')
@section('content')
    <main class="" style="font-size: 20px;">
        <div class="">
            <h1 class="text-center" style="font-size: xx-large">AUTORISATION DE VITRES TEINTÉES</h1>
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
                            <td class="">Numéro du chassis:</td>
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
                        {{ $model->created_at->format('d/m/Y à H:i') }} pour l'autorisation de la pose
                        de vitres teintées sur votre véhicule immatriculé <span
                            class="font-semibold">{{ $model->vehicle->immatriculation->number_label }}</span>
                        a été acceptée.</p>
                    <p>Conformément à la réglementation en vigueur, il est essentiel que les vitres teintées respectent le
                        pourcentage de transparence minimum requis afin de garantir la sécurité et la visibilité. Nous
                        rappelons également que l’utilisation de vitres teintées ne doit en aucun cas entraver le bon
                        fonctionnement des dispositifs de sécurité du véhicule.</p>
                    <p>Nous vous remercions de bien vouloir veiller à ce que l'installation soit effectuée par un
                        professionnel agréé. Pour toute information complémentaire, n'hésitez pas à nous contacter.</p>
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
