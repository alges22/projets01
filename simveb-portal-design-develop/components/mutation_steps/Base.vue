<script lang="ts" setup>
    import {useMutationStore} from '~/stores/mutation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Modal from "~/components/ModalComponent.vue";
    import {Form, Field, ErrorMessage} from "vee-validate";
    import * as yup from "yup";
    import {useApi} from "~/helpers/useApi";
    import {useUserStore} from "~/stores/user";

    const api = useApi()
    const store = useMutationStore()
    const {$awn} = useNuxtApp()

    let { saving, saleDeclaration, buyer } = storeToRefs(store)
    const open = ref(false)

    const userStore = useUserStore()
    const { user } = storeToRefs(userStore)

    const onSubmit = (values: any) => {
        saving.value = true

        api({
            method: 'GET',
            url: `/client/sale-declarations/${values.sale_number}`
        }).then((response) => response.data)
            .then((response) => {
                saleDeclaration.value = response

                open.value = true

                buyer.value = user.value.identity
            })
            .catch((error) => {
                $awn.alert(error.response.data.message)
            })
            .finally(() => {
                saving.value = false
            })
    }

    const schema = yup.object({
        sale_number: yup
            .string()
            .required("Veuillez renseinger le numéro de certificat de cession"),
    });
</script>

<template>
    <Modal :open="open" @close="open = false">
        <div class="px-10 md:px-10 xl:px-32 p-10">
            <div class="text-center">
                <font-awesome-icon :icon="['fast', 'circle-check']" size="2xl" style="color: #63E6BE;"/>
            </div>

            <div v-if="saleDeclaration">
                <p class="text-center font-bold text-blue-900 mb-3 mt-8 text-2xl">Certificat de cession confirmé</p>
                <hr>
                <p class="text-center mt-5 text-md">Ce véhicule a été vendu par <br>
                    <strong>{{ saleDeclaration.vehicle_owner.identity.fullName }}</strong> à
                    <strong>{{ saleDeclaration.buyer.firstname }} {{ saleDeclaration.buyer.lastname }}</strong></p>

                <div>
                    <div class="px-8 py-4 text-left bg-blue-50 mt-8">
                        <h4 class="text-blue text-xl font-bold">Vendeur</h4>
                    </div>
                    <table class="table-black">
                        <tr>
                            <td>Nom et prénoms</td>
                            <th> {{ saleDeclaration.vehicle_owner.identity.fullName }} </th>
                        </tr>
                    </table>

                    <div class="px-8 py-4 text-left bg-blue-50 mt-8">
                        <h4 class="text-blue text-xl font-bold">Acheteur</h4>
                    </div>
                    <table class="table-black">
                        <tr>
                            <td>Nom et prénoms</td>
                            <th> {{ saleDeclaration.buyer.firstname }} {{ saleDeclaration.buyer.lastname }} </th>
                        </tr>
                    </table>

                    <div class="px-8 py-4 text-left bg-blue-50 mt-8">
                        <h4 class="text-blue text-xl font-bold">Véhicule</h4>
                    </div>
                    <table class="table-black">
                        <tr>
                            <td>Marque</td>
                            <th> {{ saleDeclaration.sold_vehicle.vehicle_brand }}</th>
                        </tr>
                        <tr>
                            <td>Modèle</td>
                            <th> {{ saleDeclaration.sold_vehicle.vehicle_model }}</th>
                        </tr>
                        <tr>
                            <td>Immatriculation</td>
                            <th> {{ saleDeclaration.sold_vehicle.immatriculation }}</th>
                        </tr>
                        <tr>
                            <td>Numéro du châssis</td>
                            <th> {{ saleDeclaration.sold_vehicle.vin }}</th>
                        </tr>
                    </table>
                    <button class="btn btn-blue w-full mt-8" @click="open = false; store.nextStep()">Suivant</button>
                </div>
            </div>
        </div>
    </Modal>

    <Form :validation-schema="schema" class="card mt-4 text-center border py-16 px-4" @submit="onSubmit">
        <h4 class="text-blue font-bold text-4xl">Base</h4>

        <p class="font-bold text-2xl text-blue-900 mt-10">Vérification du certificat de cession</p>

        <div class="w-full md:w-1/2 xl:w-1/3 text-left mx-auto">
            <div class="mt-8">
                <label for="sale_number">Numéro du certificat de cession</label>
                <Field id="sale_number" class="form-control" name="sale_number" placeholder="Numéro du certificat"
                       type="text"/>
                <ErrorMessage class="form-invalid" name="sale_number"/>
            </div>

            <button :disabled="saving" class="btn btn-blue w-full mt-50">Vérifier</button>
        </div>
    </Form>
</template>

<style scoped>
</style>
