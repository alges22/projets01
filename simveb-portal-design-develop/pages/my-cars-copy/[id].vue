<script lang="ts" setup>
    import {useRoute} from "vue-router";
    import Step from "~/components/steps/Step.vue";
    import ChoiceTypeAcheteur from "~/components/my_cars_step/ChoiceTypeAcheteur.vue";
    import Cars from "~/components/my_cars_step/Cars.vue";
    import MutationDeNom from "~/components/my_cars_step/MutationDeNom.vue"
    import Resume from "~/components/my_cars_step/Resume.vue"
    import Accepter from "~/components/my_cars_step/Accepter.vue"

    const sellStore = useSellStore();

    let {activeStep, buttonLoading, service_id} = storeToRefs(sellStore)

    useHead({
        title: 'SIMVEB - Mes véhicules',
    })

    onBeforeMount(() => {
        sellStore.chargeCar();
        service_id.value = useRoute().params.id
    })

    const steps = [
        {
            'label': "Chosissez le type d'acheteur",
            'content': ChoiceTypeAcheteur,
        },
        {
            'label': "Souhaitez vous effectuer la mutation de nom ?",
            'content': MutationDeNom,
        },
        {
            'label': "Récapitulatif",
            'content': Resume,
        },
        {
            'label': "Demande de vente approuvée",
            'content': Accepter,
        }
    ];
</script>

<template>
    <div>

        <div v-if="sellStore.step == 'mes vehicules'">
            <Cars/>
        </div>
        <div v-if="sellStore.step != 'mes vehicules'">
            <Steps>
                <Step v-for="(step, index) in steps" :activeStep="activeStep" :index="index" :label="step.label"/>
            </Steps>
            <component :is="steps[activeStep].content"/>
            <div v-if="activeStep!=0" class="ms-auto w-9/12 mx-auto flex justify-end">
                <button class="btn-outline-blue mx-4">
                    <span class="font-bold text-2xl">Enregistrer</span>
                </button>
                <button :disabled="buttonLoading" class="btn-blue mx-4" @click="sellStore.nextStep()">
                    <span v-if="buttonLoading" class="font-bold text-2xl">...</span>
                    <span v-else class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>