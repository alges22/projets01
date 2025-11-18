<script lang="ts" setup>
    import ChoiceTypeAcheteur from "~/components/my_cars_step/ChoiceTypeAcheteur.vue";
    import MutationDeNom from "~/components/my_cars_step/MutationDeNom.vue";
    import Resume from "~/components/my_cars_step/Resume.vue";
    import Accepter from "~/components/my_cars_step/Accepter.vue";
    import Step from "~/components/steps/Step.vue";
    import {useRoute} from "vue-router";
    import Vehicule from "~/components/my_cars_step/Vehicule.vue";
    import {useSellStore} from "~/stores/myCarSell";
    import {storeToRefs} from "pinia";
    import Attachments from "~/components/my_cars_step/Attachments.vue";

    const sellStore = useSellStore();

    const route = useRoute()

    const id = route.params.id

    let { activeStep, service_id } = storeToRefs(sellStore)

    useHead({
        title: 'SIMVEB - Déclaration de vente',
    })

    onMounted(() => {
        service_id.value = id

        if (!sellStore.create){
            sellStore.loadCreate(id)
        }

    })

    onUnmounted(() => {
        sellStore.resetSaleDeclaration()
    })

    const steps = [
        {
            'label': "Véhicule",
            'content': Vehicule,
        },
        {
            'label': "Type d'acheteur",
            'content': ChoiceTypeAcheteur,
        },
        {
            'label': "Mutation de nom",
            'content': MutationDeNom,
        },
        {
            'label' : 'Pièces jointes',
            'content' : Attachments
        },
        {
            'label': "Récapitulatif",
            'content': Resume,
        },
        {
            'label': "Panier",
            'content': Accepter,
        }
    ];
</script>

<template>
    <Steps>
        <Step v-for="(step, index) in steps" :activeStep="activeStep" :index="index" :label="step.label"/>
    </Steps>

    <component :is="steps[activeStep].content"/>
</template>

<style scoped>
    /* Style for table rows */
    tr {
        background-color: #f2f2f2;
        /* Alternate row background color */
        border-bottom: 1px solid #ddd;
        /* Border between rows */
    }

    /* Style for table cells */
    td {
        padding: 10px;
        /* Add padding to cells for spacing */
    }

    .row.active {
        @apply bg-gray-50 rounded-xl shadow-md
    }

    .tab-container {
        @apply justify-center flex flex-row w-full mt-8
    }

    .tab {
        @apply text-gray-400 py-4 px-1 mx-4
    }

    .tab.active {
        @apply border-b-2 border-blue-600
    }
</style>