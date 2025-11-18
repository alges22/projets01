<script setup>
    import BasicButton from "@/components/BasicButton.vue";
    import {useOppositionStore} from "@/stores/opposition.js";
    import {storeToRefs} from "pinia";
    import Alert from "@/components/notification/alert.js";

    const oppositionStore = useOppositionStore()
    const {loading, vehicles, selectedVehicle} = storeToRefs(oppositionStore)
    const emit = defineEmits(["next", "prev"]);

    const next = () => {
        if (!selectedVehicle.value) {
            Alert.error("Veuillez sélectionner le véhicule concerné ");
        }

        emit('next')
    }
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-10">
        <div class="intro-y col-span-12">
            <div class="text-xl font-bold truncate mr-5">
                Sélectionnez le véhicule concerné
            </div>

            <div v-for="vehicle in vehicles" class="flex items-center justify-center dashboard-card my-4">
                <div class="w-1/4 p-1">
                    <img class="w-24 h-12 object-cover rounded-xl" :src="vehicle.image" alt="vehicle" />
                </div>
                <div class="w-1/4 p-1">
                    <span class="text-lg">{{ vehicle.vin }}</span>
                </div>
                <div class="w-1/4 p-1 text-center">
                    <span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">{{ vehicle.plate_number }}</span>
                </div>
                <div class="form-check ml-auto mr-4">
                    <input class="form-check-input" name="car-choice" :value="vehicle.id" v-model="selectedVehicle" type="radio" />
                </div>
            </div>

            <div class="text-right mt-5">
                <button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
                    Précedent
                </button>
                <BasicButton class="btn-primary w-36" @click="next" :loading="loading"> Suivant </BasicButton>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>