<script lang="ts" setup>
    import { useImmatriculationStore } from '~/stores/immatriculation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import VehiculeInfos from "~/components/partials/VehiculeInfos.vue";
    import OwnerInfos from "~/components/partials/OwnerInfos.vue";
    import PlatesInfos from "~/components/partials/PlatesInfos.vue";

    const props = defineProps<{
        type: string;
    }>();

    const store = props.type === "reimmatriculation" ? useReimmatriculationStore() :  useImmatriculationStore()

    const { $awn } = useNuxtApp()

    let { vehicule_infos, owner, data_plates, create, base_infos, saving,  attachments } = storeToRefs(store)

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
    <div class="card p-4 md:p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Récapitulatif</h4>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations de base</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 sm:grid-cols-2 gap-16">
            <table class="table-black">
                <tr>
                    <td>VIN (Numéro de châssis)</td>
                    <th>{{ base_infos.vin }}</th>
                </tr>
            </table>

            <table class="table-black">
                <tr>
                    <td>N° de déclaration de douane / Quittance</td>
                    <th>{{ base_infos.numero_douane }}</th>
                </tr>
            </table>
        </div>

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
            <OwnerInfos :owner="owner"/>
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Plaques</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 sm:grid-cols-2 gap-16">
            <PlatesInfos :data_plates="data_plates" :plate_shapes="create.plate_shapes" :plate_colors="create.plate_colors"  />
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Pièces jointes</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 sm:grid-cols-2 gap-16">
            <template v-for="document in create?.required_documents">
                <table class="table-black" v-if="attachments">
                    <tr>
                        <td>{{ document.description }}</td>
                        <th>
                            <!-- Find the attachment corresponding to the current document -->
                            <template v-if="attachments.some(attachment => attachment.type_id === document.id)">
                                {{ attachments.find(attachment => attachment.type_id === document.id).file.name }}
                            </template>
                            <template v-else>
                                Aucun fichier fourni
                            </template>
                        </th>
                    </tr>
                </table>
            </template>
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <button class="btn-blue mx-4" @click="submitDemande" :disabled="saving">
                    <span class="font-bold text-2xl">
                        Soumettre la demande
                    </span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>