<script setup>
    import * as yup from "yup";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import {useSellStore} from "~/stores/myCarSell"

    const { $awn } = useNuxtApp()

    const store = useSellStore()

    let { personneMoraleBeninois, loading, buyer } = storeToRefs(store)

    const schema = yup.object({
        enregistrement: yup
            .string()
            .required("Veuillez renseigner ce champ"),

        ifu: yup
            .string()
            .length(13, "L'IFU doit être de 13 caractères")
            .required("Veuillez renseigner le numéro NPI"),

        nom: yup
            .string()
            .required("Veuillez renseigner ce champ"),

        telephone: yup
            .string()
            .required("Veuillez renseigner ce champ"),
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
                loading.value = true
            })
    }
</script>

<template>
    <Form :validation-schema="schema" @submit="handleSubmit">
        <div class="w-full mt-3">
            <label class="font-xs mb-2" for="numeroEnregistrement">Numéro d'enregistrement</label>
            <Field v-model="personneMoraleBeninois.numeroDenregistrement" class="form-control" name="enregistrement"
                   placeholder="Numéro d'enregistrement" type="text"/>
            <ErrorMessage class="form-invalid" name="enregistrement"/>
        </div>
        <div class="w-full mt-3">
            <label class="font-xs mb-2" for="numeroIFU">Numéro IFU</label>
            <Field v-model="personneMoraleBeninois.numeroIFU" class="form-control" name="ifu"
                   placeholder="Numéro IFU" type="text"/>
            <ErrorMessage class="form-invalid" name="ifu"/>
        </div>
        <div class="w-full mt-3">
            <label class="font-xs mb-2" for="nom">Nom</label>
            <Field v-model="personneMoraleBeninois.nom" class="form-control" name="nom"
                   placeholder="Nom" type="text"/>
            <ErrorMessage class="form-invalid" name="nom"/>
        </div>
        <div class="w-full mt-3">
            <label class="font-xs mb-2" for="telephone">Téléphone</label>
            <Field v-model="personneMoraleBeninois.telephone" class="form-control" name="telephone"
                   placeholder="Téléphone" type="text"/>
            <ErrorMessage class="form-invalid" name="telephone"/>
        </div>
        <div class="text-center">
            <button class="btn-blue mx-4" type="submit">
                <span class="font-bold text-2xl">Suivant</span>
            </button>
        </div>
    </Form>
</template>

<style lang="css" scoped>

</style>