<script setup lang="ts">
    import { useMutationStore } from '~/stores/mutation'
    import Steps from "~/components/steps/Steps.vue";
    import Step from "~/components/steps/Step.vue";
    import Base from "~/components/mutation_steps/Base.vue";
    import Informations from "~/components/mutation_steps/Informations.vue";
    import Resume from "~/components/mutation_steps/Resume.vue";
    import Basket from "~/components/mutation_steps/MutationBasket.vue";
    import {useBannerStore} from "~/stores/banner";
    import Attachments from "~/components/mutation_steps/Attachments.vue";
    const store = useMutationStore()

    const bannerStore = useBannerStore()

    bannerStore.setTitle("MUTATION DE VÉHICULE")
    bannerStore.setSubTitle("Effectuez la mutation de votre véhicule en un clic pour une mise à jour instantanée de vos informations")

    const { activeStep } = storeToRefs(store)
    const steps = [
        {
            'label' : 'Base',
            'content' : Base
        },
        {
            'label' : 'Informations',
            'content' : Informations
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
        }
    ]

    const route = useRoute();

    const id = route.params.id

    definePageMeta({
        layout: "without-navbar"
    })

    useHead({
        title: 'SIMVEB - Mutation de véhicule'
    })

    onMounted(() => {
        if (!store.create){
            store.loadCreate(id)
        }
    })

    onUnmounted(() => {
        store.resetMutation()
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