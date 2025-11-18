<template>
    <Steps>
        <Step v-for="(step, index) in steps" :activeStep="activeStep" :index="index" :label="step.label"/>
    </Steps>

    <component :is="steps[activeStep].content"/>
</template>

<script setup lang="ts">
    import Step from "~/components/steps/Step.vue";
    import Vehicule from "~/components/plate_duplicate_steps/Vehicule.vue";
    import Panier from "~/components/plate_duplicate_steps/Panier.vue";
    import {storeToRefs} from "pinia";
    import {useBannerStore} from "~/stores/banner";
    import { usePlateDuplicate } from "~/stores/plateDuplicate";

    const plateDuplicateStore = usePlateDuplicate();

    let { activeStep } = storeToRefs(plateDuplicateStore)

    definePageMeta({
        layout: "without-navbar"
    })

    useHead({
        title: 'SIMVEB - Demande de duplicata de plaque'
    })

    const bannerStore = useBannerStore()
    bannerStore.setTitle("Demande de duplicata de plaque")
    bannerStore.setSubTitle("Remplissez simplement les informations requises et profitez d’un processus rapide et efficace pour obtenir votre duplicata de plaque")


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