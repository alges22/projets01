<template>
    <div class="p-16 pt-1">
        <div class="bg-blue-100 p-4 text-center">
            <span class="font-bold text-lg text-blue-500 rounded-md">Informations du véhicule</span>
        </div>
        <table class="table mt-5">
            <tr class="text-center">
                <td>Pays d'origine du véhicule</td>
                <th>Canada</th>
            </tr>
            <tr class="text-center">
                <td>Numéro du chassis</td>
                <th>{{ car.vin }}</th>
            </tr>
            <tr class="text-center">
                <td>Marque</td>
                <th>{{ car.vehicle_model }}</th>
            </tr>
            <tr class="text-center">
                <td>Modèle du véhicume</td>
                <th>BMV Série 1 M135i xDrive</th>
            </tr>
            <tr class="text-center">
                <td>Énergie</td>
                <th>Essence</th>
            </tr>
            <tr class="text-center">
                <td>Nombre de places assises</td>
                <th>{{ car.number_of_seats }} places max</th>
            </tr>
            <tr class="text-center">
                <td>Numéro moteur</td>
                <th>Illisible</th>
            </tr>
            <tr class="text-center">
                <td>Poids total (en kg)</td>
                <th>{{ car.charged_weight }}</th>
            </tr>
            <tr class="text-center">
                <td>Poids à vide (en kg)</td>
                <th>{{ car.empty_weight }}</th>
            </tr>
            <tr class="text-center">
                <td>Parc d'achat du véhicule</td>
                <th>Wanted</th>
            </tr>
            <tr class="text-center">
                <td>Année de 1ere mise en circulation</td>
                <th>{{ car.first_circulation_year }}</th>
            </tr>
            <tr class="text-center">
                <td>Type du véhicule</td>
                <th>SUV</th>
            </tr>
        </table>
        <div class="flex flex-row justify-end">
            <button class="btn-outline-primary me-5" @click="$emit('closeDetails')">Précedent</button>
            <button class="btn-outline-primary" @click="$emit('vendreEvent', car)">Vendre</button>
        </div>
    </div>
</template>

<script setup>
defineProps(['car'])
</script>

<style lang="scss" scoped></style>