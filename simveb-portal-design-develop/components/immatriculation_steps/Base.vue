<script lang="ts" setup>
    import {useImmatriculationStore} from '~/stores/immatriculation'
    import * as yup from "yup";
    import {ErrorMessage, Form, Field} from "vee-validate";
    import {useApi} from "~/helpers/useApi";

    const store = useImmatriculationStore()

    const loading = ref(false)

    const { $awn } = useNuxtApp()

    const default_values = ref({
        numero_douane: '',
        vin: ''
    })

    const api = useApi()

    const schema = yup.object({
        numero_douane: yup
            .string()
            .required("Veuillez renseigner le numéro de déclaration de douane"),

        vin: yup
            .string()
            .required("Veuillez renseinger le VIN du véhicule")
    });

    const onSubmit = (values:any) => {
        loading.value = true

        api({
            method: 'GET',
            url: `/get-vehicle?vin=${values.vin}&customs_ref=${values.numero_douane}`
        })
        .then((response) => response.data)
        .then((response) => {
            store.vehicule_infos = response

            store.base_infos = values

            store.nextStep()
        })
        .catch((error) => {
            $awn.alert(error.response.data.message)
        })
        .finally(() => {
            loading.value = false
        })
    }
</script>

<template>
    <div class="card p-4 md:p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Base</h4>

        <Form :validation-schema="schema" @submit="onSubmit" :initial-values="default_values" class="w-full md:w-2/3 lg:w-2/3 xl:w-1/3 text-left mx-auto">
            <div class="mt-8">
                <label for="immatriculation_vin">VIN (Numéro de châssis)</label>
                <Field name="vin" id="immatriculation_vin" type="text" class="form-control" placeholder="VIN (Numéro de châssis)"/>
                <ErrorMessage name="vin" class="form-invalid" />
            </div>

            <div class="mt-8">
                <label for="immatriculation_numero_douane">Numéro de déclaration de douane / Quittance</label>
                <Field id="immatriculation_numero_douane" class="form-control" placeholder="Numéro de déclaration de douane / Quittance"
                       type="text" name="numero_douane" />
                <ErrorMessage name="numero_douane" class="form-invalid" />
            </div>

            <button class="btn btn-blue w-full mt-8" type="submit" :disabled="loading">
                Vérifier
            </button>
        </Form>
    </div>
</template>

<style scoped>

</style>