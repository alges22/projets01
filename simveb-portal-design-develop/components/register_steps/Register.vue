<script setup lang="ts">
    import { useRegisterStore } from '~/stores/register'
    import { Field, Form, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup';
    import {useApi} from "~/helpers/useApi";

    const store = useRegisterStore()
    const api = useApi();

    const { $awn } = useNuxtApp()

    const defaultValues = {
        email: '',
        npi: ''
    };

    const form = ref(defaultValues);

    const submitting = ref(false)

    const schema = yup.object({
        email: yup
            .string()
            .email()
            .required("Veuillez renseigner l'email"),

        npi: yup
            .string()
            .length(10, "Le NPI doit être doit 10 caractères")
            .required("Veuillez renseigner le numéro NPI"),
    });

    function onSubmit(values : any) {
        values = { ...values, person_type: "physical" };

        store.updateInfos(values)

        submitting.value = true

        api({
            method: "POST",
            url: '/register/init',
            data: values
        }).then((response) => {
            if (response.status === 200){
                store.nextStep()
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
        <span class="text-4xl text-blue-900 font-bold mt-8 text-center">Inscription</span>

        <span class="mt-4 text-xl text-center">
            Vous avez déjà un compte SIMVeB?
            <NuxtLink class="text-blue-900 text-base" to="login">
                Identifiez - vous ici?
            </NuxtLink>
        </span>

        <Form :validation-schema="schema" @submit="onSubmit" class="w-full md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8 text-left">
            <div>
                <label class="block text-lg font-bold text-black">Email</label>
                <Field name="email" type="email" v-model="form.email"  class="form-control" placeholder="Email" />
                <ErrorMessage name="email" class="form-invalid" />
            </div>

            <div>
                <label class="block text-lg font-bold text-black">Numéro NIP</label>
                <Field name="npi" type="text" v-model="form.npi"  class="form-control" placeholder="Numéro NIP" />
                <ErrorMessage name="npi" class="form-invalid" />
            </div>

            <button class="btn-primary w-full mt-10" :disabled="submitting">
                <template v-if="submitting">...</template>
                <template v-else>Créer un compte</template>
            </button>
        </Form>
    </div>
</template>

<style scoped>

</style>