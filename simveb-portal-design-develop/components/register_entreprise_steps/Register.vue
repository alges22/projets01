<script setup lang="ts">
    import { useRegisterStore } from '~/stores/register'
    import { Field, Form, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup';
    import {useApi} from "~/helpers/useApi";

    const store = useRegisterStore()
    const api = useApi();

    const { $awn } = useNuxtApp()

    const defaultValues = {
        ifu: ''
    };

    const form = ref(defaultValues);

    const submitting = ref(false)

    const schema = yup.object({
        ifu: yup
            .string()
            .length(13, "L'IFU doit être de 13 caractères")
            .required("Veuillez renseigner le numéro NPI"),
    });

    function onSubmit(values : any) {
        values = { ...values, person_type: "moral" };


        submitting.value = true

        api({
            method: "POST",
            url: '/register/init',
            data: values
        }).then((response) => {
            if (response.status === 200){
                store.updateInfos(values)

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
                <label class="block text-lg font-bold text-black">IFU</label>
                <Field name="ifu" type="text" v-model="form.ifu"  class="form-control" placeholder="Numéro IFU" />
                <ErrorMessage name="ifu" class="form-invalid" />
            </div>

            <button class="btn-primary w-full mt-4" :disabled="submitting">
                <template v-if="submitting">...</template>
                <template v-else>Créer un compte</template>
            </button>
        </Form>
    </div>
</template>

<style scoped>

</style>