<script setup lang="ts">
    import { useRegisterStore } from '~/stores/register'
    import InputCode from "~/components/InputCode.vue";
    import {useApi} from "~/helpers/useApi";

    const api = useApi()
    const store = useRegisterStore()

    const { $awn } = useNuxtApp()

    const code = ref("")
    const submitting = ref(false)

    const checkOtp = () => {
        if (code.value === ""){
            $awn.alert("Veuillez renseigner le code OTP")

            return
        }

        submitting.value = true

        api({
            method: "POST",
            url: '/register/check-otp',
            data: {
                "person_type": store.person_type,
                "ifu": store.ifu,
                "otp": code.value
            }
        }).then((response) => {
            if (response.status === 200){
                store.setCompanyData(response.data.company_data)

                store.nextStep()
            }
        }).catch((error) => {
            $awn.alert(error.response.data.error)
        }).finally(() => {
            submitting.value = false
        })
    }

    const resendOtp = () => {
        api({
            method: "POST",
            url: '/register/resend-otp',
            data: {
                "person_type": store.person_type,
                "ifu": store.ifu,
            }
        }).then((response) => {
            if (response.status === 200){
                $awn.success(response.data.message)
            }
        }).catch((error) => {
            $awn.alert(error.response.data.error.message)
        })
    }
</script>

<template>
    <div class="flex flex-col">
        <div class="w-full md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8 text-left">
            <span class="text-4xl text-blue-900 font-bold mt-8">Processus de vérification</span>

            <div class="mt-10">
                <span class="mt-16 text-xl">Un code unique de vérification vous à été envoyé sur votre numéro de téléphone. Veuillez entrer le code ici </span>
            </div>
        </div>

        <div class="w-full md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8 text-left">

            <InputCode :length="4" v-model="code" />

            <div class="mt-10">
                <span class="text-xl">Vous n'avez pas reçu de code ? <NuxtLink @click="resendOtp" class="text-blue-900 text-base">Renvoyer</NuxtLink> </span>
            </div>

            <button class="btn-primary w-full" @click="checkOtp" :disabled="submitting">
                <template v-if="submitting">...</template>
                <template v-else>Confirmer le code</template>
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>