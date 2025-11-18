<template>
    <div class="card p-20 mt-2">
        <div class="w-9/12 text-center p-2 mx-auto">
            <h1 class="font-bold text-3xl text-blue-900 rounded-md">
                Souhaitez vous effectuer la mutation de nom ?
            </h1>

            <div class="flex flex-col mt-8">
                <div :class="{ 'border-blue-500 text-blue-500 font-semibold': mutation === true }" @click="selectOption(true)" class="p-3 py-5 rounded-lg border my-2">
                    <input id="oui" :checked="mutation === true" class="btn mx-3 rounded p-3" type="radio">
                    <label class="text-lg" for="oui">
                        Oui je procèderais à la mutation de nom
                    </label>
                </div>

                <div :class="{ 'border-blue-500 text-blue-500 font-semibold': mutation === false }" @click="selectOption(false)" class="p-3 py-5 rounded-lg border my-2">
                    <input id="non" :checked="mutation === false" class="btn mx-3 rounded p-3" type="radio">
                    <label class="text-lg" for="non">
                        Non l'acheteur éffectuera la mutation
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon :icon="['fas', 'arrow-left']" class="mx-4"/>
                Précédent
            </button>

            <div class="ms-auto">
                <!--                    <button class="btn-outline-blue mx-4" @click="store.storeDemande(id)" :disabled="saving">-->
                <!--                        <span class="font-bold text-2xl">Enregistrer</span>-->
                <!--                    </button>-->

                <button @click="store.nextStep()" class="btn-blue mx-4">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>

    </div>
</template>

<script setup>
    import {useSellStore} from "~/stores/myCarSell.js";

    const store = useSellStore()

    let { mutation } = storeToRefs(store)

    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    // Method to update mutationAccepted based on user selection
    const selectOption = (value) => {
        mutation.value = value;
    };
</script>

<style lang="scss" scoped></style>