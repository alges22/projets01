<script setup>
    import {useApi} from "~/helpers/useApi";
    import Informations from "~/components/immatriculation_steps/Informations.vue";
    import Attachments from "~/components/immatriculation_steps/Attachments.vue";
    import Plate from "~/components/immatriculation_steps/Plate.vue";
    import Step from "~/components/steps/Step.vue";
    import Steps from "~/components/steps/Steps.vue";
    import {useImmatriculationStore} from "~/stores/immatriculation.js";

    const demand = ref(null)

    const route = useRoute();

    const id = route.params.id

    const api = useApi()

    const store = useImmatriculationStore()
    const { activeStep, loading, update, data_plates, vehicule_infos } = storeToRefs(store)

    const steps = [
        {
            'label' : 'Informations',
            'content': Informations
        },
        {
            'label' : 'Plaque',
            'content' : Plate
        },
        {
            'label' : 'Pièces jointes',
            'content' : Attachments
        }
    ]

    onMounted(() => {
        update.value = true

        api({
            method: 'GET',
            url: `/client/demands/edit/${id}`
        }).then((response) => response.data)
            .then((response) => {
                data_plates.value =  {
                    plate_color_id: response.demand.immatriculation.plate_color_id,
                    front_plate_shape_id: response.demand.immatriculation.front_plate_shape_id,
                    back_plate_shape_id: response.demand.immatriculation.back_plate_shape_id,
                }

                vehicule_infos.value = response.vehicle

                demand.value = response.demand

                if (!store.create){
                    store.loadCreate(response.demand.service.id)
                }
            })

    })

    onUnmounted(() => {
        store.resetImmatriculation()
    })
</script>



<template>
    <div v-if="loading" class="flex justify-center mt-16">
        <Loader />
    </div>
    <template v-else>
        <Steps>
            <Step v-for="(step, index) in steps" :label="step.label" :index="index" :activeStep="activeStep" />
        </Steps>

        <component :is="steps[activeStep].content" />
    </template>
</template>