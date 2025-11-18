<script lang="ts" setup>

import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
    <div class="card p-16">
        <div class="flex-row flex">
            <h4 class="font-bold text-blue-900 text-2xl">Factures fiscales</h4>

            <div class="ms-auto flex-row flex">
                <RouterLink class="font-bold text-blue-500 text-lg px-8" to="/transactions/paiements">
                    Voir mes paiements

                    <font-awesome-icon class="text-gray-400 font-medium ms-2" icon="fa fa-right-from-bracket"/>
                </RouterLink>
                <RouterLink class="font-bold text-blue-500 text-lg px-8" to="/transactions">
                    Voir mes transactions

                    <font-awesome-icon class="text-gray-400 font-medium ms-2" icon="fa fa-right-from-bracket"/>
                </RouterLink>
            </div>
        </div>

        <table class="classic-table mt-10">
            <thead>
            <tr class="font-bold">
                <td>Services</td>
                <td>Référence</td>
                <td>Date</td>
                <td>Montant</td>
                <td class="text-center">Statut</td>
                <td class="text-center">Télécharger</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Achat de véhicule</td>
                <td>AN-2023-50212356</td>
                <td>05/02/2023</td>
                <td>10 000 FCFA</td>
                <td class="text-center"><font-awesome-icon icon="fa fa-check" class="text-green-600" /></td>
                <td class="text-center">
                    <font-awesome-icon :icon="['fas', 'download']" class="icon" />
                </td>
            </tr>
            <tr>
                <td>Mutation</td>
                <td>AN-2023-50212356</td>
                <td>05/02/2023</td>
                <td>10 806 000 FCFA</td>
                <td class="text-center"><font-awesome-icon icon="fa fa-check" class="text-green-600" /></td>
                <td class="text-center">
                    <font-awesome-icon :icon="['fas', 'download']" class="icon" />
                </td>
            </tr>
            <tr>
                <td>Transformation de véhicule</td>
                <td>AN-2023-50212356</td>
                <td>05/02/2023</td>
                <td>50 000 FCFA</td>
                <td class="text-center"><font-awesome-icon icon="fa fa-times" class="text-red-600" /></td>
                <td class="text-center">
                    <font-awesome-icon :icon="['fas', 'download']" class="icon" />
                </td>
            </tr>
            </tbody>
        </table>

    </div>
</template>

<style scoped>
    .classic-table td,
    .classic-table th {
        @apply pt-4 pb-2 border-gray-100
    }

    .classic-table{
        @apply text-gray-500 text-left w-full
    }

    .classic-table thead td,
    .classic-table thead th {
        @apply pt-4 pb-2 border-b-2 border-gray-100
    }


    .icon{
        @apply p-2 bg-blue-500 px-4 text-white
    }
</style>