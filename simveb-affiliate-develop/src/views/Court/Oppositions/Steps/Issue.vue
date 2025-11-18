<script setup>
    import TextInputGroup from "@/components/Form/TextInputGroup.vue";
    import BasicButton from "@/components/BasicButton.vue";
    import {useOppositionStore} from "@/stores/opposition.js";
    import {storeToRefs} from "pinia";


    const oppositionStore = useOppositionStore()
    const {custom_ref, loading, errors, isCompany} = storeToRefs(oppositionStore)
    const emit = defineEmits(["next", "prev"]);

    const findVehicles = () => {
        oppositionStore
            .findVehicles()
            .then(() => {
                emit('next')
            })
    }
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-10" @submit.prevent="findVehicles">
        <form class="intro-y col-span-12">
            <div class="text-xl font-bold truncate mr-5">
                Recherchez les véhicules d'un individu ou d'une personne morale
            </div>

            <div class="intro-y box p-5 mt-4">
                <div class="flex flex-col justify-between mx-4">
                    <span class="text-xl font-bold text-center mb-4">{{ title }}</span>
                    <div class="grid grid-cols-2 gap-6 rounded-2xl">
                        <label
                            class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
                            for="seller_type_moral"
                        >
						<span class="form-check ml-auto ms-4">
							<input
                                id="seller_type_moral"
                                v-model="isCompany"
                                class="form-check-input"
                                name="seller-type"
                                type="radio"
                                :value="false"
                            />
						</span>
                            <span class="w-full mx-4"> NPI </span>
                        </label>
                        <label
                            class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
                            for="seller_type_physique"
                        >
						<span class="form-check ml-auto ms-4">
							<input
                                id="seller_type_physique"
                                v-model="isCompany"
                                class="form-check-input"
                                name="seller-type"
                                type="radio"
                                :value="true"
                            />
						</span>
                            <span class="w-full mx-4"> IFU </span>
                        </label>
                    </div>
                    <div class="mt-4">
                        <TextInputGroup
                            v-model="custom_ref"
                            :name="!isCompany ? 'npi' : 'ifu'"
                            :disabled="loading"
                            required
                            :placeholder="!isCompany ? 'Entrer le NPI' : 'Entrer l\'IFU'"
                            :pattern="!isCompany ? '[0-9]{10}' : '[0-9]{13}'"
                            :maxlength="!isCompany ? 10 : 13"
                            :errors="errors.npi_or_ifu"
                        />
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

<style scoped lang="scss">

</style>