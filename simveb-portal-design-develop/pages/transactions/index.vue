<script lang="ts" setup>

import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
    <div class="card p-16 mt-8">
        <div class="flex-row flex">
            <h4 class="font-bold text-blue-900 text-2xl">Mes transactions</h4>

<!--            <div class="ms-auto flex-row flex">-->
<!--                <RouterLink class="font-bold text-blue-500 text-lg px-8" to="/transactions/paiements">-->
<!--                    Voir mes paiements-->

<!--                    <font-awesome-icon class="text-gray-400 font-medium ms-2" icon="fa fa-right-from-bracket"/>-->
<!--                </RouterLink>-->
<!--                <RouterLink class="font-bold text-blue-500 text-lg px-8" to="/transactions/factures">-->
<!--                    Voir les factures-->

<!--                    <font-awesome-icon class="text-gray-400 font-medium ms-2" icon="fa fa-right-from-bracket"/>-->
<!--                </RouterLink>-->
<!--            </div>-->
        </div>

<!--        <div class="table mt-10">-->
<!--            <span class="w-1/5">Date</span>-->
<!--            <span class="w-2/5">Référence de la transaction</span>-->
<!--            <span class="w-1/5">Montant</span>-->
<!--            <span class="w-1/5">Statut</span>-->
<!--            <span class="w-1/5"></span>-->
<!--        </div>-->


<!--        <details class="group shadow-lg">-->
<!--            <summary class="table bg-blue-100 border-blue-300 border">-->
<!--                <span class="w-1/5">05/01/2024</span>-->
<!--                <span class="w-2/5">SIV-012448135489524</span>-->
<!--                <span class="w-1/5">300 550 FCFA</span>-->
<!--                <span class="w-1/5">Payé</span>-->
<!--                <span class="w-1/5 font-bold">-->
<!--                    Détail <i class="fa-solid fa-house"></i>-->
<!--                </span>-->
<!--            </summary>-->
<!--            <div class="text-neutral-600 bg-gray-100 group-open:animate-fadeIn px-10 py-5 border mt-2 rounded-2xl">-->
<!--                <table class="classic-table">-->
<!--                    <tbody>-->
<!--                    <tr>-->
<!--                        <td>Achat de véhicule</td>-->
<!--                        <td>AN-2023-50212356</td>-->
<!--                        <td>10 000 FCFA</td>-->
<!--                        <td class="text-center"><font-awesome-icon icon="fa fa-check" class="text-green-600" /></td>-->
<!--                        <td class="text-center">-->
<!--                            <font-awesome-icon :icon="['fas', 'download']" class="icon" />-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <td>Mutation</td>-->
<!--                        <td>AN-2023-50212356</td>-->
<!--                        <td>10 806 000 FCFA</td>-->
<!--                        <td class="text-center"><font-awesome-icon icon="fa fa-check" class="text-green-600" /></td>-->
<!--                        <td class="text-center">-->
<!--                            <font-awesome-icon :icon="['fas', 'download']" class="icon" />-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    </tbody>-->
<!--                </table>-->
<!--            </div>-->
<!--        </details>-->

        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <strong class="font-bold">Fonctionnalité non disponible</strong>
        </div>
    </div>
</template>

<style scoped>
    .table{
        @apply flex-row flex text-gray-400
    }

    .table > span{
        @apply py-4 px-8
    }

    .classic-table td,
    .classic-table th {
        @apply pt-2 pb-1 border-gray-200 border-b-2
    }

    .classic-table{
        @apply text-gray-500 text-left w-full
    }


    .icon{
        @apply p-2 bg-blue-500 px-4 text-white
    }
</style>