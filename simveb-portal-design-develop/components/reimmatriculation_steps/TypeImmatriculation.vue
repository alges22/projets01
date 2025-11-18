<script setup lang="ts">
    const store = useReimmatriculationStore()
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    let { parameter, create } = storeToRefs(store)
</script>

<template>
    <div class="card p-4 md:py-16 md:px-8 lg:px-16 xl:px-32 mt-4 text-center">
        <div class="grid grid-cols-2">
            <div>
                <img src="/car.png" alt="..">
            </div>

            <div class="text-center px-8">
                <h4 class="text-xl">Veuillez sélectionner le type d'immatriculation</h4>

                <label
                    v-for="service in create.services"
                    class="has-[:checked]:bg-blue-200 bg-blue-100 flex items-center text-blue-600 text-lg font-bold mt-4 rounded-3xl h-16 cursor-pointer px-8"
                    :for="service.id"
                >
                    <span> {{ service.name }} </span>

                    <span class="form-check ms-auto">
                       <input
                           :id="service.id"
                           v-model="parameter"
                           class="form-check-input"
                           name="seller-type"
                           type="radio"
                           :value="service.code"
                       />
                    </span>
                </label>
            </div>

        </div>

        <div class="flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <!--                    <button class="btn-outline-blue mx-4" @click="store.storeDemande(serviceId)" :disabled="saving">-->
                <!--                        <span class="font-bold text-2xl">Enregistrer</span>-->
                <!--                    </button>-->

                <button class="btn-blue mx-4">
                    <span class="font-bold text-2xl" @click="store.nextStep()">Suivant</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>