<script setup lang="ts">
    import { useDepotDeTitreStore } from '~/stores/depotDeTitre'
    import { useBannerStore } from "~/stores/banner";
    import Steps from "~/components/steps/Steps.vue";
    import Step from "~/components/steps/Step.vue";
    import Base from "~/components/depot_de_titre_steps/Base.vue";
    import Basket from "~/components/depot_de_titre_steps/Basket.vue";
    import Resume from '~/components/depot_de_titre_steps/Resume.vue'
    import Attachments from "~/components/depot_de_titre_steps/Attachments.vue";
    import {storeToRefs} from "pinia";

    const route = useRoute()

    const bannerStore = useBannerStore()
    bannerStore.setTitle("DEPÔT OU REPRISE DE TITRE")

    const store = useDepotDeTitreStore()

    let { activeStep } = storeToRefs(store)

    const id = route.params.id

    const steps = [
        {
            'label' : 'Base',
            'content' : Base
        },
        {
            'label' : 'Pièces',
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
        title: 'SIMVEB - Dépôt de titre'
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