<script lang="ts" setup>
    import { useDepotDeTitreStore } from '~/stores/depotDeTitre'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import VehiculeInfos from "~/components/partials/VehiculeInfos.vue";
    import {storeToRefs} from "pinia";

    const store = useDepotDeTitreStore()
    const userStore = useUserStore()

    const route = useRoute();

    const id = route.params.id

    const { $awn } = useNuxtApp()

    let { vehicule_infos, loading, title_reason_id, create } = storeToRefs(store)
    const { user } = storeToRefs(userStore)

    const currentDate =  new Date().toLocaleDateString()

    // Compute the title reason label based on `title_reason_id`
    const getTitleReasonLabel = () => {
        const matchedReason = create.value.title_reasons.find(reason => reason.id === title_reason_id.value);
        return matchedReason ? matchedReason.label : 'Unknown';
    };

    const submitDemande = () => {
        store.addToCart(id, user.value.identity.npi)
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
    <div class="card py-16 px-4 md:px-16 lg:px-32 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Recapitulatif</h4>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Détail du dépôt de titre</h4>
        </div>
        <div class="px-8 py-4 grid grid-cols-1 gap-16">
            <table class="table-black">
                <tr>
                    <td>Date</td>
                    <th>{{ currentDate }}</th>
                </tr>
                <tr>
                    <td>Motif du dépôt de titre</td>
                    <th>{{ getTitleReasonLabel() }}</th>
                </tr>
            </table>
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations du véhicule</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 md:grid-cols-2 gap-16">
            <VehiculeInfos :vehicule_infos="vehicule_infos" />
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <button class="btn-blue mx-4" @click="submitDemande" :disabled="loading">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>