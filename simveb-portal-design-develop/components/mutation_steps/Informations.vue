<script lang="ts" setup>
    import {useMutationStore} from '~/stores/mutation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import dayjs from "dayjs";
    import VehiculeInfos from "~/components/partials/VehiculeInfos.vue";
    import OwnerInfos from "~/components/partials/OwnerInfos.vue";

    const store = useMutationStore()

    let { saleDeclaration } = storeToRefs(store)
</script>

<template>
    <div class="card px-2 md:px-10 xl:px-32 p-10 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Informations</h4>
        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Base</h4>
        </div>
        <div v-if="saleDeclaration" class="px-8 py-4 grid grid-cols-1 md:grid-cols-2">
            <table class="table-black">
                <tr>
                    <td>Numéro du certificat de cession</td>
                    <th>{{ saleDeclaration.reference }}</th>
                </tr>
            </table>
            <table class="table-black">
                <tr>
                    <td>Vente officialisée le</td>
                    <th>{{ dayjs(saleDeclaration.created_at).format('DD/MM/YYYY') }}</th>
                </tr>
            </table>
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations du véhicule</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 md:grid-cols-2 gap-16">
            <VehiculeInfos :vehicule_infos="saleDeclaration.sold_vehicle" />
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations du propriétaire</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 md:grid-cols-2 gap-16">
            <OwnerInfos :owner="saleDeclaration.vehicle_owner.identity" />
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
<!--                <button class="btn-outline-blue mx-4">-->
<!--                    <span class="font-bold text-2xl">Enregistrer</span>-->
<!--                </button>-->

                <button class="btn-blue mx-4" @click="store.nextStep()">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>