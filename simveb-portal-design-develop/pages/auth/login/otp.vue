<script setup lang="ts">
    import { useLoginStore } from "~/stores/login";
    import InputCode from "~/components/InputCode.vue";
    import { useAuthStore } from "~/stores/auth";
    import { useApi } from "~/helpers/useApi";

    const store = useLoginStore()
    const authStore = useAuthStore()
    const { $awn } = useNuxtApp()

    definePageMeta({
        layout: "auth"
    })

    useHead({
        title: 'SIMVEB - Connexion',
    })

    const code = ref("")
    const api = useApi()

    onMounted(() => {
        if (store.npi === "")
            navigateTo('/auth/login')
    })

    const checkOtp = async () => {
        if (!code.value) {
            $awn.alert("Veuillez renseigner le code OTP")
            return
        }

        try {
            await authStore.authenticateUser({
                "username": store.npi,
                "password": code.value,
                "grant_type": "password",
                "client_id" : import.meta.env.VITE_CLIENT_ID,
                "client_secret" : import.meta.env.VITE_CLIENT_SECRET
            })

            $awn.success("Content de vous revoir!!")

            window.location.replace('/')

        } catch (error) {
            $awn.alert(error.response.data.message)
            // Handle authentication error
        }
    }

    const resendOtp = async () => {
        try {
            const response = await api({
                method: "POST",
                url: '/login/resend-otp',
                data: {
                    "npi": store.npi,
                }
            })
            if (response.status === 200) {
                $awn.success("Code OTP renvoyé avec succès")
            }
        } catch (error) {
            $awn.alert("Erreur lors de l'envoi du code OTP")
        }
    }
</script>


<template>
    <div class="flex flex-col">
        <div class="w-full md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8 text-left">
            <span class="text-4xl text-blue-900 font-bold mt-8">Code de connexion</span>

            <div class="mt-10">
                <span class="mt-16 text-xl">Un code unique de vérification vous à été envoyé sur votre numéro de téléphone. Veuillez entrer le code ici </span>
            </div>
        </div>

        <div class="w-full md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8 text-left">

            <InputCode :length="4" v-model="code" />

            <div class="mt-10">
                <span class="text-xl cursor-pointer">Vous n'avez pas reçu de code ? <NuxtLink @click="resendOtp" class="text-blue-900 text-base">Renvoyer</NuxtLink> </span>
            </div>

            <button class="btn-primary w-full mt-4" @click="checkOtp" :disabled="authStore.loading">
                <template v-if="authStore.loading">...</template>
                <template v-else>Se connecter à mon espace</template>
            </button>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>