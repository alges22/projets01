<script setup>
    import BasicButton from "@/components/BasicButton.vue";
    import FileInputGroup from "@/components/Form/FileInputGroup.vue";
    import {usePledgeStore} from "@/stores/pledge.js";
    import {storeToRefs} from "pinia";
    import {useOtpStore} from "@/stores/otp.js";
    import Alert from "@/components/notification/alert.js";
    import { useRouter } from "vue-router";
    import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
    import {userHasPermissions} from "@/helpers/permissions.js";

    const emit = defineEmits(["next", "prev"]);

    const router = useRouter()

    const pledgeStore = usePledgeStore();
    const {files, errors, loading, financial_institution, create } = storeToRefs(pledgeStore)

    const otpStore = useOtpStore();
    const { authorization_id } = storeToRefs(otpStore);

    const { can } = userHasPermissions();

    const savePledge = () => {
        pledgeStore
            .savePledge(authorization_id.value)
            .then(response => {
                Alert.success("Enregistrement effectué avec succès");

                router.push('/court-pledges')
            })
    }
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-10">
        <form class="intro-y col-span-12"  @submit.prevent="savePledge">
            <div class="intro-y box p-5">
                <div>
                    <div class="sm:grid grid-cols-2 gap-8 mb-8">
                        <div>
                            <FileInputGroup
                                :multiple="true"
                                v-model="files"
                                name="files"
                                label="Dossier de gage"
                                add-class="w-full"
                                :disabled="loading"
                                :errors="errors.files"
                                required
                                auto-complete="files"
                            />
                        </div>
                        <div v-if="can('store-pledge-by-distributor')">
                            <SelectInputGroup
                                v-model="financial_institution"
                                name="reason"
                                label="Banque"
                                add-class="w-full"
                                :disabled="loading"
                                :errors="errors.financial_institution"
                                required
                                auto-complete="vin"
                                option-text="acronym"
                                option-value="id"
                                :options="create.financial_institutions"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-5">
                <button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
                    Annuler
                </button>
                <BasicButton class="btn-primary w-36" type="submit" :loading="loading"> Confirmer </BasicButton>
            </div>
        </form>
    </div>
</template>

<style scoped lang="scss">

</style>