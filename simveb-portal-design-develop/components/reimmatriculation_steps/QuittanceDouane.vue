<script setup lang="ts">
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    const store = useReimmatriculationStore()

    let { base_infos, with_immatriculation, parameter } = storeToRefs(store)

    const modal = ref(false)

    const schema = yup.object({
        quittance: yup
            .string()
            .required("Veuillez renseinger la quittance de douane")
    });

    const onSubmit = (values:any) => {

    }
</script>

<template>
    <ModalComponent :open="modal" @close="modal = false">
        <div class="card p-4 md:py-16 md:px-8 lg:px-16 xl:px-32 mt-4 text-center">
            <h4 class="text-xl">Souhaitez-vous garder le même numéro d'immatriculation?</h4>

            <div class="w-full grid grid-cols-2 mt-4 gap-8">
                <label
                    class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
                    for="oui"
                >
                    <span class="form-check ml-auto">
                       <input
                           @click="parameter = 'withoutImmatriculation' "
                           id="oui"
                           v-model="with_immatriculation"
                           class="form-check-input"
                           name="seller-type"
                           type="radio"
                           :value="false"
                       />
                        <span class="w-full mx-4"> Oui </span>
                    </span>
                </label>
                <label
                    class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
                    for="non"
                >
                    <span class="form-check ml-auto">
                       <input
                           id="non"
                           @click="parameter = 'withImmatriculation' "
                           v-model="with_immatriculation"
                           class="form-check-input"
                           name="seller-type"
                           type="radio"
                           :value="true"
                       />
                        <span class="w-full mx-4"> Non </span>
                    </span>
                </label>
            </div>
            <button class="btn btn-blue w-full" @click="store.nextStep()">Suivant</button>
        </div>
    </ModalComponent>

    <div class="card p-4 md:py-16 md:px-8 lg:px-16 xl:px-32 mt-4 text-center">
        <div class="flex-row flex">
            <div>
                <img src="/cogs.png" alt="..">
            </div>
            <Form :validation-schema="schema" @submit="onSubmit" class="w-full text-left mt-auto py-16">
                <div class="mt-8">
                    <label for="immatriculation_vin">Veuillez entrer la quittance de douance</label>
                    <Field :v-model="base_infos.numero_douane" name="quittance" id="quittance" type="text" class="form-control" placeholder="Quittance de douane"/>
                    <ErrorMessage name="vin" class="form-invalid" />
                </div>
            </Form>
        </div>

        <div class="flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <!--                    <button class="btn-outline-blue mx-4" @click="store.storeDemande(serviceId)" :disabled="saving">-->
                <!--                        <span class="font-bold text-2xl">Enregistrer</span>-->
                <!--                    </button>-->

                <button class="btn-blue mx-4">
                    <span class="font-bold text-2xl" @click="modal = true">Suivant</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>