
<script setup>
    import {useSellStore} from "~/stores/myCarSell"
    import * as yup from "yup";
    import {ErrorMessage, Field, Form} from "vee-validate";

    const { $awn } = useNuxtApp()

    const store = useSellStore()
    let { nipBuyer, loading, buyer } = storeToRefs(store)

    const schema = yup.object({
        npi: yup
            .string()
            .length(10, "Le NPI doit être doit 10 caractères")
            .required("Veuillez renseigner le numéro NPI")
    });

    const handleSubmit = () => {
        loading.value = true

        store.getBuyerInformation(nipBuyer)
            .then((data) => {
                buyer.value = data;

                store.nextStep()
            })
            .catch((error) => {
                $awn.alert(error)
            })
            .finally(() => {
                loading.value = false
            })
    }
</script>

<template>
    <Form :validation-schema="schema" @submit="handleSubmit">
        <div class="w-full mt-3">
            <label class="text-lg  mb-2" for="npi">Numéro d'Identification Personnel (NPI)</label>
            <Field
                v-model="nipBuyer"
                class="form-control"
                name="npi"
                placeholder="NPI" type="text" />
            <ErrorMessage class="form-invalid" name="npi"/>
        </div>

        <div class="text-center">
            <button class="btn-blue mx-4" type="submit" :disabled="loading">
                <span class="font-bold text-2xl">Suivant</span>
            </button>
        </div>
    </Form>
</template>


<style lang="css" scoped>

</style>