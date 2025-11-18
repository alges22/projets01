<script setup lang="ts">
    import {useLoginStore} from "~/stores/login";

    definePageMeta({
        layout: "auth"
    })

    useHead({
        title: 'SIMVEB - Connexion',
    })

    const { $awn } = useNuxtApp()

    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    import {useApi} from "~/helpers/useApi";

    const store = useLoginStore()

    const api = useApi()

    const defaultValues = {
        npi: ''
    };

    const submitting = ref(false)

    const form = ref(defaultValues);

    const schema = yup.object({
        npi: yup
            .string()
            .length(10, "Le NPI doit être doit 10 caractères")
            .required("Veuillez renseigner le numéro NPI"),
    });

    const onSubmit = (values : any) => {
        submitting.value = true

        api({
            method: "POST",
            url: '/login/send-otp',
            data: values
        }).then((response) => {
            if (response.status === 200){
                store.setNpi(values.npi)

                navigateTo('/auth/login/otp')
            }
        }).catch((error) => {
            $awn.alert(error.response.data.message)
        }).finally(() => {
            submitting.value = false
        })
    }
</script>


<template>
    <div class="flex flex-col">
        <span class="text-4xl text-blue-900 font-bold mt-8 text-center">Connexion</span>

        <span class="mt-4 text-xl text-center">Vous n'avez pas de compte SIMVeB?
            <NuxtLink class="text-blue-900 text-base" to="register">
                Inscrivez - vous ici?
            </NuxtLink>
        </span>

        <Form :validation-schema="schema" @submit="onSubmit" class="w-full md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8 text-left">
            <div>
                <label class="block text-lg font-bold text-black">Numéro NIP</label>
                <Field name="npi" type="text" v-model="form.npi"  class="form-control" placeholder="Numéro NIP" />
                <ErrorMessage name="npi" class="form-invalid" />
            </div>

            <button class="btn-primary w-full mt-4" :disabled="submitting">
                <template v-if="submitting">...</template>
                <template v-else>Vérification</template>
            </button>
        </Form>
    </div>
</template>


<style lang="sass">

</style>