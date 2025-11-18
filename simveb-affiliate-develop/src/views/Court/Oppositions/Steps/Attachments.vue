<script setup>
    import BasicButton from "@/components/BasicButton.vue";
    import {useOppositionStore} from "@/stores/opposition.js";
    import {storeToRefs} from "pinia";
    import FileInputGroup from "@/components/Form/FileInputGroup.vue";
    import {useOtpStore} from "@/stores/otp.js";
    import OtpModalForm from "@/views/OtpModalForm.vue";
    import {ref} from "vue";
    import Alert from "@/components/notification/alert.js";
    import {useRouter} from "vue-router";

    const router = useRouter()

    const oppositionStore = useOppositionStore()
    const {opposition_file, loading, errors, custom_ref, isCompany} = storeToRefs(oppositionStore)
    const emit = defineEmits(["next", "prev"]);

    const otpModalOpen = ref(false);

    const goNext = (authorization_id) => {
        otpModalOpen.value = false;

        oppositionStore
            .storeOpposition(authorization_id)
            .then((response) => {
                Alert.success("Demande d'opposition enregistrée avec succès")

                router.push('/oppositions')
            })
    };
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-10" @submit.prevent="otpModalOpen = !otpModalOpen">
        <form class="intro-y col-span-12">
            <div class="text-xl font-bold truncate mr-5">
                Pièces jointes
            </div>

            <div class="intro-y box p-5 mt-4">
                <div>
                    <div class="sm:grid grid-cols-1 gap-8 mb-8">
                        <div>
                            <FileInputGroup
                                :multiple="true"
                                v-model="opposition_file"
                                :disabled="loading"
                                :errors="errors.opposition_file"
                                add-class="w-full"
                                label="Sélectionnez les pièces jointes"
                                name="opposition_file"
                                required
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-5">
                <button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
                    Précédent
                </button>
                <BasicButton :loading="loading" class="btn-primary w-36" type="submit"> Valider </BasicButton>
            </div>
        </form>
    </div>

    <OtpModalForm
        :is-company="isCompany"
        :npi="custom_ref"
        :open="otpModalOpen"
        @close="otpModalOpen = false"
        @submit="(authorization_id) => goNext(authorization_id)"
    />
</template>

<style lang="scss" scoped>

</style>