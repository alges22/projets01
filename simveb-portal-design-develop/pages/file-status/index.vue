<script setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {useApi} from "~/helpers/useApi";
    import {useServiceStore} from "~/stores/service.js";

    const api = useApi()

    const demands = ref([])

    const currentStepIndices = ref([])

    const serviceStore = useServiceStore()

    onMounted(() => {
        api({
            method: "GET",
            url: 'client/demands?_no_paginate=true',
        })
            .then((response) => response.data)
            .then((response) => {
                demands.value = response.data

                currentStepIndices.value = response.data.map(demand => {
                    return demand.steps.findIndex(step => step.is_current);
                });
            })
    })

    useHead({
        title: 'SIMVEB - Status des dossiers',
    })
</script>

<template>
    <div class="card p-16">
        <div class="text-center mb-8">
            <span class="font-medium text-xl">
                Ici vous pouvez consulter l'état d'avancement de toutes vos demandes de service
            </span>
        </div>

        <div v-for="(demand, demandIndex) in demands" :key="demand.id" class="py-1">
            <details class="group shadow-md">
                <summary class="bg-blue-50 px-10 py-5 rounded-t-2xl flex flex-row cursor-pointer">
                    <span class="text-xl font-bold text-blue-900"> {{ demand.service }} / {{ demand.reference }}</span>
                    <span class="transition group-open:rotate-180 ms-auto">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor"
                             stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24"
                             width="24"><path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </span>
                </summary>

                <div class="text-neutral-600 group-open:animate-fadeIn px-10 py-5 border-b rounded-b-2xl">
                    <p>
                        Votre demande d'immatriculation progresse rapidement, nous sommes presque à la fin du processus.
                    </p>

                    <div class="grid grid-cols-5 gap-8 mt-8">
                        <div v-for="(step) in demand.steps" :key="step.id">
                            <div :class="{'active': step.is_done, 'current': step.is_current}" class="form-status-step">
                                {{ step.label }}
                            </div>
                            <div v-if="step.is_done" class="py-8 text-center text-2xl text-green-600">
                                <font-awesome-icon icon="fa fa-check-double"/>
                            </div>

                            <div v-if="step.is_current" class="py-8 text-center text-sm text-yellow-600">
                                <span>Votre dossier se trouve actuellement à cette étape</span>
                            </div>
                        </div>
                    </div>

<!--                    <div v-if="demand.is_editable">-->
<!--                        <NuxtLink :to="serviceStore.getEditLink({-->
<!--                            code: demand.service_code,-->
<!--                            id: demand.id-->
<!--                        })">-->
<!--                            <font-awesome-icon icon="fa fa-pencil"/>-->
<!--                            Modifier la demande-->
<!--                        </NuxtLink>-->
<!--                    </div>-->
                </div>
            </details>
        </div>
    </div>
</template>

<style scoped>

</style>