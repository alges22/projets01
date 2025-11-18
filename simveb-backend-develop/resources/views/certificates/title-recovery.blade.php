@extends('pdf.layout')
@section('content')
    <main class="">
        <div class="mt-6 mb-8">
            <h3 class="font-semibold text-3xl text-center underline">CERTIFICAT DE DÉPOT DE TITRE</h3>
        </div>
        <section class="mb-8">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Détail du dépot de titre</h4>
            </div>
            <div class="text-base px-4">
                <table class="table-fixed">
                    <tbody class="">
                    <tr class="border-b p-4">
                        <td class="">Date</td>
                        <td class="font-semibold">{{ $titleRecovery->created_at }}</td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Dépôt de titre</td>
                        <td class="font-semibold">{{ $titleRecovery->dosit->reason->label }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-8">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Informations du véhicule</h4>
            </div>
            <div class="text-base px-4">
                <table class="table-fixed">
                    <tbody class="">
                    <tr class="border-b p-4">
                        <td class="">Pays d'origine du véhicule</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Numéro du chassis</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Marque</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Modèle de véhicule</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Energie</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Nb de place assise</td>
                        <td class="font-semibold"></td>
                    </tr>
                    </tbody>
                </table>
                <table class="table-fixed">
                    <tbody class="">
                    <tr class="border-b p-4">
                        <td class="">Numéro Moteur</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Poids total</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Poids à vide</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Parc d'achat du véhicule</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Année de 1ere mise en circulation</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Type de véhicule</td>
                        <td class="font-semibold"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="">
            <div class="bg-blue-100">
                <h4 class="px-4 py-2 text-2xl text-blue-600">Informations du propriétaire</h4>
            </div>
            <div class="grid grid-cols-2 gap-12 text-base px-4">
                <table class="table-fixed">
                    <tbody class="">
                    <tr class="border-b p-4">
                        <td class="">Nom</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Prénoms</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Date de naissance</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Lieu de naissance</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Nationalité</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Sexe</td>
                        <td class="font-semibold"></td>
                    </tr>
                    </tbody>
                </table>
                <table class="table-fixed">
                    <tbody class="">
                    <tr class="border-b p-4">
                        <td class="">NPI</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Réference du BFU</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Téléphone</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Adresse email</td>
                        <td class="font-semibold"></td>
                    </tr>
                    <tr class="border-b p-4">
                        <td class="">Domicile</td>
                        <td class="font-semibold"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@stop
