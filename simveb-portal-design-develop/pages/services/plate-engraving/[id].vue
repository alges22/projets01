<script setup lang="ts">
    import { usePlateEngraving } from '~/stores/plateEngraving'
    import {storeToRefs} from "pinia";
    import { useBannerStore } from "~/stores/banner";
    import Steps from "~/components/steps/Steps.vue";
    import Step from "~/components/steps/Step.vue";
    import Basket from "~/components/plate_engraving/Basket.vue";
    import Resume from '~/components/plate_engraving/Resume.vue'
    import Attachments from "~/components/plate_engraving/Attachments.vue";
    import Vehicule from "~/components/plate_engraving/Vehicule.vue";

    const route = useRoute()

    const bannerStore = useBannerStore()
    bannerStore.setTitle("Gravage de vitre teintée")

    const store = usePlateEngraving()

    let { activeStep } = storeToRefs(store)

    const id = route.params.id

    const steps = [
        {
            'label': "Véhicule",
            'content': Vehicule,
        },
        {
            'label' : 'Pièces jointes',
            'content' : Attachments
        },
        {
            'label' : 'Récapitulatif',
            'content' : Resume
        },
        {
            'label' : 'Panier',
            'content' : Basket
        },
    ]

    useHead({
        title: 'SIMVEB - Gravage de vitre teintée'
    })

    onMounted(() => {
        if (!store.create){
            store.loadCreate(id)
        }
    })
</script>
<template>
    <Steps>
        <Step v-for="(step, index) in steps" :label="step.label" :index="index" :activeStep="activeStep" />
    </Steps>

    <component :is="steps[activeStep].content" />
</template>


<style scoped>

</style>