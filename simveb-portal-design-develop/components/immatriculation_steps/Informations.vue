<script lang="ts" setup>
    import { useImmatriculationStore } from '~/stores/immatriculation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {useUserStore} from "~/stores/user";
    import VehiculeInfos from "~/components/partials/VehiculeInfos.vue";
    import OwnerInfos from "~/components/partials/OwnerInfos.vue";

    const { $awn } = useNuxtApp()

    const store = useImmatriculationStore()
    const userStore = useUserStore()

    const { owner, vehicule_infos, loading, saving, activeStep, update } = storeToRefs(store)

	const route = useRoute();
	const id = route.params.id

    const next = () => {
        if (update){
            store.nextStep()
        }else{
            store.storeDemande(id)
                .then(() => {
                    store.nextStep()
                })
                .catch((error) => {
                    $awn.alert(error)
                })
        }
    }

    onMounted(() => {
        if (!store.owner){
            store.getOwner(userStore.user.identity.npi)
        }
    })
</script>

<template>
    <div class="card p-4 md:py-16 md:px-8 lg:px-16 xl:px-32 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl mb-4">Informations</h4>

        <template v-if="loading">
            <Loader />
        </template>
        <template v-else>
            <div class="px-8 py-4 text-left bg-blue-50 mt-8">
                <h4 class="text-blue text-2xl font-bold">Informations du véhicule</h4>
            </div>

            <div class="px-8 py-4 grid grid-cols-1 sm:grid-cols-2 gap-16">
                <VehiculeInfos :vehicule_infos="vehicule_infos" />
            </div>

            <div class="px-8 py-4 text-left bg-blue-50 mt-8">
                <h4 class="text-blue text-2xl font-bold">Informations du propriétaire</h4>
            </div>

            <div class="px-8 py-4 grid grid-cols-1 sm:grid-cols-2 gap-16">
                <OwnerInfos v-if="owner" :owner="owner" />
            </div>

            <div class="mt-16 flex-row flex">
                <button v-if="activeStep !== 0" class="text-blue font-bold text-2xl" @click="store.previousStep()">
                    <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                    Précédent
                </button>

                <div class="ms-auto">
<!--                    <button class="btn-outline-blue mx-4" @click="store.storeDemande(serviceId)" :disabled="saving">-->
<!--                        <span class="font-bold text-2xl">Enregistrer</span>-->
<!--                    </button>-->

                    <button class="btn-blue mx-4" @click="next" :disabled="saving">
                        <span class="font-bold text-2xl">Suivant</span>
                    </button>
                </div>
            </div>
        </template>
    </div>

</template>

<style scoped>

</style>