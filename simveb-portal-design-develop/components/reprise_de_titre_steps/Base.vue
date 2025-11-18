<script lang="ts" setup>
    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    import {useApi} from "~/helpers/useApi";
    import { useRepriseDeTitreStore } from "~/stores/repriseDeTitre";

    const api = useApi()

    const { $awn } = useNuxtApp()

    const store = useRepriseDeTitreStore()

    let { vin, loading, vehicule_infos } = storeToRefs(store)

    const schema = yup.object({
        vin: yup
            .string()
            .required("Veuillez renseinger le VIN du véhicule"),
    });

    const onSubmit = (values: any) => {
        loading.value = true

        api({
            method: 'GET',
            url: `/get-vehicle?vin=${values.vin}`
        })
            .then((response) => response.data)
            .then((response) => {
                vehicule_infos.value = response

                if (response.title_deposits.length > 0){
                    store.nextStep()
                }else{
                    $awn.alert("Ce véhicule n'a pas subit de dépôt de titre")
                }

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
    <Form :validation-schema="schema" @submit="onSubmit" class="card py-16 px-4 mt-4 text-center">
        <p class="font-bold text-2xl text-blue-900">Reprise de titre</p>

        <div class="w-full md:w-1/2 xl:w-1/3 text-left mx-auto mt-8">
            <div class="mt-4">
                <label for="vin">VIN (Numéro de châssis)</label>
                <Field type="text" id="vin" name="vin" class="form-control" placeholder="VIN (Numéro de châssis)" v-model="vin" />
                <ErrorMessage name="vin" class="form-invalid" />
            </div>

            <button class="btn btn-blue w-full mt-5" type="submit" :disabled="loading">Suivant</button>
        </div>
    </Form>
</template>
