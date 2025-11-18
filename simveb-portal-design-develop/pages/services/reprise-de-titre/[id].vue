<script setup lang="ts">
    import Steps from "~/components/steps/Steps.vue";
    import Step from "~/components/steps/Step.vue";
    import Base from "~/components/reprise_de_titre_steps/Base.vue";
    import Basket from "~/components/reprise_de_titre_steps/Basket.vue";
    import Resume from '~/components/reprise_de_titre_steps/Resume.vue'
    import Attachments from "~/components/reprise_de_titre_steps/Attachments.vue";
    import {useBannerStore} from "~/stores/banner";
    import {storeToRefs} from "pinia";

    const route = useRoute()

    const bannerStore = useBannerStore()
    bannerStore.setTitle("REPRISE DE TITRE")

    const store = useRepriseDeTitreStore()

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

    definePageMeta({
        layout: "without-navbar"
    })

    useHead({
        title: 'SIMVEB - Reprise de titre'
    })

    onMounted(() => {
        if (!store.create){
            store.loadCreate(id)
        }
    })

    onUnmounted(() => {
        store.resetRepriseDeTitre()
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