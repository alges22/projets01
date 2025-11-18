<template>
    <Steps>
        <Step v-for="(step, index) in steps" :activeStep="activeStep" :index="index" :label="step.label"/>
    </Steps>

    <component :is="steps[activeStep].content"/>
</template>
<script setup lang="ts">
    import Step from "~/components/steps/Step.vue";
    import Vehicule from "~/components/gray_card_duplicate_steps/Vehicule.vue";
    import {storeToRefs} from "pinia";
    import {useGrayCardDuplicateStore} from "~/stores/grayCardDuplicate";
    import {useRoute} from "vue-router";
    import Panier from "~/components/gray_card_duplicate_steps/Panier.vue";
    import {useBannerStore} from "~/stores/banner";

    const grayCardDuplicateStore = useGrayCardDuplicateStore();

    let { activeStep } = storeToRefs(grayCardDuplicateStore)

    const route = useRoute()

    definePageMeta({
        layout: "without-navbar"
    })

    useHead({
        title: 'SIMVEB - Demande de duplicata de carte grise'
    })

    const bannerStore = useBannerStore()
    bannerStore.setTitle("Demande de duplicata de carte grise")
    bannerStore.setSubTitle("Remplissez simplement les informations requises et profitez d’un processus rapide et efficace pour obtenir votre duplicata de carte grise.")

    const id = route.params.id

    const steps = [
        {
            'label': "Véhicule",
            'content': Vehicule,
        },
        {
            'label': "Panier",
            'content': Panier,
        }
    ];
</script>