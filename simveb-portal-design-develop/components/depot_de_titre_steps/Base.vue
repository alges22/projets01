<script lang="ts" setup>
    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    import {useApi} from "~/helpers/useApi";
    import {storeToRefs} from "pinia";
    import {useDepotDeTitreStore} from "~/stores/depotDeTitre";

    const api = useApi()

    const { $awn } = useNuxtApp()

    const store = useDepotDeTitreStore()

    let { vin, loading, vehicule_infos, title_reason_id, create, title_deposit } = storeToRefs(store)

    const isModalOpen = ref(false)

    const schemaMotif = yup.object({
        motif: yup
            .string()
            .required("Veuillez sélectionner le motif")
    });

    const schema = yup.object({
        vin: yup
            .string()
            .required("Veuillez renseinger le VIN du véhicule"),
    });

    const handleMotif = (values: any) => {
        isModalOpen.value = false

        store.nextStep()
    }

    const onSubmit = (values: any) => {
        loading.value = true

        api({
            method: 'GET',
            url: `/get-vehicle?vin=${values.vin}`
        })
            .then((response) => response.data)
            .then((response) => {
                if (response.title_deposits.length > 0){
                    $awn.alert("Ce véhicule à déjà subit un dépôt de titre")
                }else{
                    vehicule_infos.value = response

                    title_deposit.value = response.title_deposits[0]

                    isModalOpen.value = true
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
    <ModalComponent :open="isModalOpen" @close="isModalOpen = false">
        <Form :validation-schema="schemaMotif" @submit="handleMotif" class="card py-16 px-4 mt-4 text-center">
            <div class="w-1/2 text-left mx-auto">
                <div>
                    <label for="motif">Sélectionnez le motif</label>
                    <Field as="select" class="form-control" id="motif" name="motif" v-model="title_reason_id">
                        <option value="">Choisissez un motif</option>
                        <option v-for="title_reason in create?.title_reasons" :value="title_reason.id"> {{ title_reason.label }} </option>
                    </Field>
                    <ErrorMessage name="motif" class="form-invalid" />
                </div>

                <button class="btn btn-blue w-full mt-5" type="submit">Suivant</button>
            </div>
        </Form>
    </ModalComponent>


    <Form :validation-schema="schema" @submit="onSubmit" class="card py-16 px-4 mt-4 text-center">
        <p class="font-bold text-2xl text-blue-900">Dépot de titre</p>

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
