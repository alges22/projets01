<script setup lang="ts">
    const props = defineProps({
        vehicule_infos: {
            type: Object,
            required: true,
        },
    })

    const getCharacteristicName = (characteristic) => {
        return Object.keys(characteristic)[0];
    }
    const getCharacteristicValue = (characteristic) => {
        return characteristic[getCharacteristicName(characteristic)];
    }
</script>

<template>
    <table class="table-black">
        <tr>
            <td>Pays d'origine du véhicule</td>
            <th>{{ vehicule_infos.origin_country }}</th>
        </tr>
        <tr>
            <td>Numéro du chassis</td>
            <th>{{ vehicule_infos.vin }}</th>
        </tr>
        <tr>
            <td>Marque</td>
            <th>{{ vehicule_infos.vehicle_brand }} </th>
        </tr>
        <tr>
            <td>Modèle du véhicule</td>
            <th>{{ vehicule_infos.vehicle_model }}</th>
        </tr>
        <tr>
            <td>Numéro moteur</td>
            <th>{{ vehicule_infos.engin_number }}</th>
        </tr>
        <tr>
            <td>Parc d'achat du véhicule</td>
            <th>{{ vehicule_infos.park }} </th>
        </tr>
        <tr>
            <td>Année de 1ere mise en circulation</td>
            <th>{{ vehicule_infos.first_circulation_year}} </th>
        </tr>
    </table>

    <table class="table-black">
        <tr v-for="(characteristic, index) in vehicule_infos.characteristics" :key="index">
            <td>{{ getCharacteristicName(characteristic) }}</td>
            <th>{{ getCharacteristicValue(characteristic) }}</th>
        </tr>
    </table>
</template>

<style scoped lang="scss">

</style>