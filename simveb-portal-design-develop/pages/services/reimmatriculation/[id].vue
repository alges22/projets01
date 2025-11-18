<script lang="ts" setup>
    import Steps from "~/components/steps/Steps.vue";
    import Step from "~/components/steps/Step.vue";
    import {useBannerStore} from "~/stores/banner";
    import {useReimmatriculationStore} from "~/stores/reimmatriculation";
    import Base from "~/components/reimmatriculation_steps/Base.vue";
    import ReimmatriculationBasket from "~/components/reimmatriculation_steps/ReimmatriculationBasket.vue";
    import QuittanceDouane from "~/components/reimmatriculation_steps/QuittanceDouane.vue";
    import TypeImmatriculation from "~/components/reimmatriculation_steps/TypeImmatriculation.vue";
    import Plate from "~/components/immatriculation_steps/Plate.vue";
    import Attachments from "~/components/immatriculation_steps/Attachments.vue";
    import Resume from "~/components/immatriculation_steps/Resume.vue";
    import Label from "~/components/immatriculation_steps/Label.vue";
    import Number from "~/components/immatriculation_steps/Number.vue";
    import ReimmatriculationReason from "~/components/reimmatriculation_steps/ReimmatriculationReason.vue";

    definePageMeta({
        layout: "without-navbar"
    })

    useHead({
        title: 'SIMVEB - Demande de réimmatriculation'
    })

    const bannerStore = useBannerStore()
    bannerStore.setTitle("Demande de réimmatriculation")
    bannerStore.setSubTitle("Remplissez le formulaire de demande et laissez-nous vous guider à travers le processus, vous permettant de reprendre la route rapidement et sans traces")

    const store = useReimmatriculationStore()
    const {activeStep, parameter} = storeToRefs(store)

    const route = useRoute();
    const id = route.params.id

    let steps = ref([])

    const allSteps = {
        default: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Motif de réimmatriculation', 'content': ReimmatriculationReason },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': "Type d'immatriculation", 'content': TypeImmatriculation },
            { 'label': 'Numéro', 'content': Number, 'props': { type: 'reimmatriculation' } },
            { 'label': 'Label', 'content': Label, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Plaques', 'content': Plate, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Pièces jointes', 'content': Attachments, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Récaputulatif', 'content': Resume, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Panier', 'content': ReimmatriculationBasket},
        ],
        withoutImmatriculation: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': 'Plaques', 'content': Plate, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Pièces jointes', 'content': Attachments, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Récaputulatif', 'content': Resume, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Panier', 'content': ReimmatriculationBasket},
        ],
        withImmatriculation: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': "Type d'immatriculation", 'content': TypeImmatriculation },
        ],
        immatriculationStandart: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': "Type d'immatriculation", 'content': TypeImmatriculation },
            { 'label': 'Plaques', 'content': Plate, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Pièces jointes', 'content': Attachments, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Récaputulatif', 'content': Resume, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Panier', 'content': ReimmatriculationBasket},
        ],
        immatriculationPrestigeNumber: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': "Type d'immatriculation", 'content': TypeImmatriculation },
            { 'label': 'Numéro', 'content': Number, 'props': { type: 'reimmatriculation' } },
            { 'label': 'Plaques', 'content': Plate, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Pièces jointes', 'content': Attachments, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Récaputulatif', 'content': Resume, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Panier', 'content': ReimmatriculationBasket},
        ],
        immatriculationPrestigeLabel: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': "Type d'immatriculation", 'content': TypeImmatriculation },
            { 'label': 'Label', 'content': Label, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Plaques', 'content': Plate, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Pièces jointes', 'content': Attachments, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Récaputulatif', 'content': Resume, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Panier', 'content': ReimmatriculationBasket},
        ],
        immatriculationPrestigeNumberLabel: [
            { 'label': 'Base', 'content': Base },
            { 'label': 'Quittance de douance', 'content': QuittanceDouane },
            { 'label': "Type d'immatriculation", 'content': TypeImmatriculation },
            { 'label': 'Numéro', 'content': Number, 'props': { type: 'reimmatriculation' } },
            { 'label': 'Label', 'content': Label, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Plaques', 'content': Plate, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Pièces jointes', 'content': Attachments, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Récaputulatif', 'content': Resume, 'props': { type: 'reimmatriculation'} },
            { 'label': 'Panier', 'content': ReimmatriculationBasket},
        ],
    }

    const configureSteps = (parameter) => {
        if (parameter === "withoutImmatriculation") {
            steps.value = allSteps.withoutImmatriculation
        } else if (parameter === "IMMATRICULATION_STANDARD") {
            steps.value = allSteps.immatriculationStandart
        } else if (parameter === "IMMATRICULATION_PRESTIGE_NUMBER") {
            steps.value = allSteps.immatriculationPrestigeNumber
        } else if (parameter === "IMMATRICULATION_PRESTIGE_LABEL") {
            steps.value = allSteps.immatriculationPrestigeLabel
        } else if (parameter === "IMMATRICULATION_PRESTIGE_NUMBER_LABEL") {
            steps.value = allSteps.immatriculationPrestigeNumberLabel
        }else {
            steps.value = allSteps.default
        }
    }


    watch(
        () => parameter.value,
        (newParam) => {
            configureSteps(newParam)
        },
        { immediate: true }
    )


    onMounted(() => {
        if (!store.create) {
            store.loadCreate(id)
        }

        configureSteps("default")
    })

    //zezy

    onUnmounted(() => {
        store.resetReimmatriculation()
    })
</script>

<template>
    <Steps>
        <Step v-for="(step, index) in steps" :activeStep="activeStep" :index="index" :label="step.label"/>
    </Steps>

    <component :is="steps[activeStep]?.content" v-bind="steps[activeStep]?.props || {}"/>
</template>

<style scoped>

</style>