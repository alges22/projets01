<script setup>
    import {useBannerStore} from "~/stores/banner.js";
    import Step from "~/components/steps/Step.vue";
    import {useRoute} from "vue-router";
    import {storeToRefs} from "pinia";
    import {useVehiculeTransformation} from "~/stores/vehiculeTransformation.js";
    import TypeTransformation from "~/components/vehicule_transformation/TypeTransformation.vue";
    import Attachments from "~/components/vehicule_transformation/Attachments.vue";
    import Basket from "~/components/vehicule_transformation/Basket.vue";
    import {useApi} from "~/helpers/useApi";
    import VehiculeShow from "~/components/vehicule_transformation/VehiculeShow.vue";

    useHead({
        title: 'SIMVEB - Transformation de véhicule'
    })

    definePageMeta({
        layout: "without-navbar"
    })

    const bannerStore = useBannerStore()
    bannerStore.setTitle("Transformation de véhicule")
    bannerStore.setSubTitle("Remplissez facilement les informations nécessaires et bénéficiez d'un processus d'immatriculation rapide et efficace")

    const store = useVehiculeTransformation();

    const api = useApi()

    const route = useRoute()
    const id = route.params.id

    let { activeStep, create, update } = storeToRefs(store)

    const steps = [
        {
            'label': "Véhicule",
            'content': VehiculeShow,
        },
        {
            'label': "Transformations",
            'content': TypeTransformation,
        },
        {
            'label': "Pièces jointes",
            'content': Attachments,
        },
        {
            'label': "Récapitulatif",
            'content': Basket,
        },
    ];

    onMounted(() => {
        update.value = true

        api({
            method: 'GET',
            url: `/client/demands/edit/${id}`
        }).then((response) => response.data)
            .then((response) => {

                if (!create.value) {
                    store.loadCreate(response.demand.service.id)
                }
            })
    })


    onUnmounted(() => {
        store.resetVehicleTransformation()
    })
</script>

<template>
    <Steps>
        <Step v-for="(step, index) in steps" :activeStep="activeStep" :index="index" :label="step.label"/>
    </Steps>

    <component :is="steps[activeStep].content"/>
</template>