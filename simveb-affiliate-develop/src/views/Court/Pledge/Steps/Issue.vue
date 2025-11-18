<script setup>
    import {usePledgeStore} from "@/stores/pledge.js";
    import TextInputGroup from "@/components/Form/TextInputGroup.vue";
    import {storeToRefs} from "pinia";
    import BasicButton from "@/components/BasicButton.vue";
    const emit = defineEmits(["next", "prev"]);

    const pledgeStore = usePledgeStore()
    const {vin, custom_ref, loading, errors} = storeToRefs(pledgeStore)

    const findVehicle = () => {
        pledgeStore
            .getRecapInfos()
            .then(() => {
                emit('next')
            })
            .catch((error) => {
                console.log(error)
            })
    }
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-10" @submit.prevent="findVehicle">
        <form class="intro-y col-span-12">
            <div class="intro-y box p-5">
                <div>
                    <div class="sm:grid grid-cols-2 gap-8 mb-8">
                        <div>
                            <TextInputGroup
                                v-model="vin"
                                name="vin"
                                label="Entrer le VIN du véhicule"
                                add-class="w-full"
                                :disabled="loading"
                                :errors="errors.vin"
                                required
                                auto-complete="vin"
                            />
                        </div>

                        <div>
                            <TextInputGroup
                                v-model="custom_ref"
                                name="custom_ref"
                                label="Entrer le numéro d'immatriculation"
                                add-class="w-full"
                                :disabled="loading"
                                :errors="errors.custom_ref"
                                required
                                auto-complete="custom_ref"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-5">
                <button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
                    Annuler
                </button>
                <BasicButton class="btn-primary w-36" type="submit" :loading="loading"> Vérifier</BasicButton>
            </div>
        </form>
    </div>
</template>

<style lang="scss" scoped>

</style>