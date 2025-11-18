<script setup>
    import {useVehiculeTransformation} from "~/stores/vehiculeTransformation";
    import VehiculeInfos from "~/components/partials/VehiculeInfos.vue";
    import {useApi} from "~/helpers/useApi";
    import {useRoute} from "vue-router";

    const route = useRoute()
    const id = route.params.id

    const store = useVehiculeTransformation();

    const { vehicule_infos, update, loading, transformations, transformation } = storeToRefs(store)

    const next = () => {
        store.nextStep();
    }

    const api = useApi()

    onMounted(() => {
        update.value = true

        api({
            method: 'GET',
            url: `/client/demands/edit/${id}`
        }).then((response) => response.data)
            .then((response) => {
                transformation.value = response.demand.transformation.id

                transformations.value = response.demand.transformation.transformation_characteristics.map((transformation) => {
                    return {
                        transformation_id: transformation.id,
                        category_id: transformation.new_characteristic.category_id,
                        characteristic_id: transformation.new_characteristic.id,
                        transformationType: transformation.new_characteristic.category.types[0].label,
                        type_id: transformation.new_characteristic.category.types[0].id
                    }
                })

                vehicule_infos.value = response.vehicle

                if (!store.create){
                    store.loadCreate(response.demand.service.id)
                }
            })
    })
</script>

<template>
    <div v-if="loading" class="text-center">
        <Loader />
    </div>
    <div v-else class="card p-4 md:p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Véhicule</h4>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations du véhicule</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-1 sm:grid-cols-2 gap-16">
             <VehiculeInfos :vehicule_infos="vehicule_infos" />
        </div>

        <div class="mt-16 flex-row flex">
            <div class="ms-auto">
                <button class="btn-blue mx-4" @click="next">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
/* Your SCSS code goes here */
</style>
