<script lang="ts" setup>
    import {useMutationStore} from '~/stores/mutation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import VehiculeInfos from "~/components/partials/VehiculeInfos.vue";
    import OwnerInfos from "~/components/partials/OwnerInfos.vue";

    const store = useMutationStore()

    let { saleDeclaration, saving } = storeToRefs(store)

    const { $awn } = useNuxtApp()

    const route = useRoute();

    const id = route.params.id

    const submitDemande = () => {
        store.addToCart(id)
            .then((response) => {
                $awn.success('Demande enregistrée avec succès')

                store.nextStep()
            })
            .catch((error) => {
                $awn.alert(error)
            })
    }
</script>

<template>
    <div class="card py-16 px-32 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Récapitulatif</h4>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations du véhicule</h4>
        </div>
        <div class="px-8 py-4 grid grid-cols-1 md:grid-cols-2 gap-16">
            <VehiculeInfos :vehicule_infos="saleDeclaration.sold_vehicle" />
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations sur l'acheteur</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-2 gap-16">
            <OwnerInfos :owner="saleDeclaration.buyer" />
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

                <button :disabled="saving" class="btn-blue mx-4" @click="submitDemande()">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>