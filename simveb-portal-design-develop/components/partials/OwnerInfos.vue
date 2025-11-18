<script setup lang="ts">
    const props = defineProps({
        owner: {
            type: Object,
            required: true,
        },
    })
</script>

<template>
    <table class="table-black">
        <tr>
            <td>Nom</td>
            <th>{{ owner.lastname }}</th>
        </tr>
        <tr>
            <td>Prénoms</td>
            <th>{{ owner.firstname }}</th>
        </tr>
        <tr>
            <td>Date de naissance</td>
            <th>{{ owner.birth_date }}</th>
        </tr>
        <tr>
            <td>Lieu de naissance</td>
            <th>{{ owner.birth_place }}</th>
        </tr>
        <tr>
            <td>Nationalité</td>
            <th>{{ owner.origin_country }}</th>
        </tr>
        <tr>
            <td>Sexe</td>
            <th>{{ owner.gender }}</th>
        </tr>
    </table>

    <table class="table-black">
        <tr>
            <td>NPI</td>
            <th>{{ owner.npi }}</th>
        </tr>
        <tr>
            <td>Référence de BFU</td>
            <th>{{ owner.bfu }}</th>
        </tr>
        <tr>
            <td>Téléphone</td>
            <th>{{ owner.telephone }}</th>
        </tr>
        <tr>
            <td>Adresse email</td>
            <th>{{ owner.email }}</th>
        </tr>
        <tr>
            <td>Domicile</td>
            <th>{{ owner.address }}</th>
        </tr>
    </table>
</template>

<style scoped lang="scss">

</style>