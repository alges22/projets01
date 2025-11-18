<script setup lang="ts">
    import { useTintedWindowsStore } from '~/stores/tintedWIndows'
    import {storeToRefs} from "pinia";
    import { useBannerStore } from "~/stores/banner";
    import Steps from "~/components/steps/Steps.vue";
    import Step from "~/components/steps/Step.vue";
    import Basket from "~/components/tinted_windows/Basket.vue";
    import Resume from '~/components/tinted_windows/Resume.vue'
    import Attachments from "~/components/tinted_windows/Attachments.vue";
    import Vehicule from "~/components/tinted_windows/Vehicule.vue";

    const route = useRoute()

    const bannerStore = useBannerStore()
    bannerStore.setTitle("Authorisation de vitre teintée")

    const store = useTintedWindowsStore()

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
        title: 'SIMVEB - Autorisation de vitre teintée'
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