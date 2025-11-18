<script setup>
    import BasicButton from "@/components/BasicButton.vue";
    import {useOppositionStore} from "@/stores/opposition.js";
    import {storeToRefs} from "pinia";
    import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";

    const oppositionStore = useOppositionStore()
    const {loading, errors, reason_for_opposition, create} = storeToRefs(oppositionStore)
    const emit = defineEmits(["next", "prev"]);
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-10" @submit.prevent="$emit('next')">
        <form class="intro-y col-span-12">
            <div class="text-xl font-bold truncate mr-5">
                Sélectionnez le motif de l'opposisition
            </div>

            <div class="intro-y box p-5 mt-4">
                <div>
                    <div class="sm:grid grid-cols-1 gap-8 mb-8">
                        <div>
                            <SelectInputGroup
                                v-model="reason_for_opposition"
                                name="reason"
                                label="Motif de l'opposition"
                                add-class="w-full"
                                :disabled="loading"
                                :errors="errors.npi_or_ifu"
                                required
                                auto-complete="vin"
                                option-text="label"
                                option-value="id"
                                :options="create.reasons"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-5">
                <button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
                    Précédent
                </button>
                <BasicButton class="btn-primary w-36" type="submit" :loading="loading"> Suivant</BasicButton>
            </div>
        </form>
    </div>
</template>

<style scoped lang="scss">

</style>