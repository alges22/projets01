<script setup>
    import {useRegisterStore} from '~/stores/register'
    import ModalComponent from "~/components/ModalComponent.vue";
    import * as yup from "yup";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import {useApi} from "~/helpers/useApi";

    const store = useRegisterStore()

    const api = useApi()

    const {$awn} = useNuxtApp()

    const open = ref(false)

    const data = ref({
        states: [],
        towns: [],
        districts: [],
        villages: [],
    })

    const submitting = ref(false)
    const loading = ref(false)

    const schema = yup.object({
        state_id: yup
            .string()
            .required("Veuillez renseigner le département"),

        district_id: yup
            .string()
            .required("Veuillez renseigner l'arrondissement"),

        village_id: yup
            .string()
            .required("Veuillez renseigner le village ou quartier"),

        house: yup
            .string()
            .required("Veuillez renseigner la maison"),

        town_id: yup
            .string()
            .required("Veuillez renseigner la commune"),
    });

    function onSubmit(values) {
        values = {...values, person_type: "physical", npi: store.npi};

        submitting.value = true

        api({
            method: 'POST',
            url: '/register/store',
            data: values
        }).then((response) => {
            if (response.status === 200) {
                $awn.success(response.data.message)

                navigateTo('/auth/login')
            }
        }).catch((error) => {
            console.log(error)

            $awn.alert(error.response.data.message)
        }).finally(() => {
            submitting.value = false
        })
    }

    const updateTowns = ($event) => {

        const stateId = $event.target.value

        api({
            method: 'GET',
            url: `/registration/search/towns?state_id=${stateId}`
        }).then(response => response.data)
            .then(response => {
                data.value.towns = response
            })
            .finally(() => {
                loading.value = false
            })
    }

    const updateDistricts = ($event) => {
        loading.value = true
        const townId = $event.target.value

        api({
            method: 'GET',
            url: `/registration/search/districts?town_id=${townId}`
        }).then(response => response.data)
            .then(response => {
                data.value.districts = response
            })
            .finally(() => {
                loading.value = false
            })
    }

    const updateVillages = ($event) => {
        loading.value = true
        const villageId = $event.target.value

        api({
            method: 'GET',
            url: `/registration/search/villages?district_id=${villageId}`
        }).then(response => response.data)
            .then(response => {
                data.value.villages = response
            })
            .finally(() => {
                loading.value = false
            })
    }

    onMounted(() => {
        api({
            method: 'GET',
            url: '/registration/search/states'
        }).then(response => response.data)
            .then(response => {
                data.value.states = response
            })
    })
</script>

<template>
    <div class="flex flex-col">
        <ModalComponent :open="open" @close="open = !open">
            <div class="px-16 py-16">
                <div class="text-center mb-10">
                    <span class="text-2xl text-blue-900 font-bold mt-8"> Quelle est votre adresse actuelle ? </span>
                </div>

                <Form :validation-schema="schema" @submit="onSubmit">
                    <div class="grid grid-cols-1 gap-2">
                        <div>
                            <label class="block text-lg font-bold text-black">Département</label>
                            <Field as="select" class="form-control" name="state_id" @change="updateTowns">
                                <option value="">Selectionnez le département</option>
                                <option v-for="state in data.states" :value="state.id"> {{ state.name }}</option>
                            </Field>
                            <ErrorMessage class="form-invalid" name="state_id"/>
                        </div>

                        <div v-if="data.towns.length > 0">
                            <label class="block text-lg font-bold text-black">Commune</label>
                            <Field as="select" class="form-control" name="town_id" @change="updateDistricts">
                                <option value="">Selectionnez la commune</option>
                                <option v-for="town in data.towns" :value="town.id"> {{ town.name }}</option>
                            </Field>
                            <ErrorMessage class="form-invalid" name="town_id"/>
                        </div>

                        <div v-if="data.districts.length > 0">
                            <label class="block text-lg font-bold text-black">Arrondissements</label>
                            <Field as="select" class="form-control" name="district_id" @change="updateVillages">
                                <option value="">Selectionnez l'arrondissement</option>
                                <option v-for="district in data.districts" :value="district.id"> {{
                                        district.name
                                    }}
                                </option>
                            </Field>
                            <ErrorMessage class="form-invalid" name="district_id"/>
                        </div>

                        <div v-if="data.villages.length > 0">
                            <label class="block text-lg font-bold text-black">Quartier ou village</label>
                            <Field as="select" class="form-control" name="village_id">
                                <option value="">Selectionnez le quartier ou village</option>
                                <option v-for="village in data.villages" :value="village.id"> {{
                                        village.name
                                    }}
                                </option>
                            </Field>
                            <ErrorMessage class="form-invalid" name="village_id"/>
                        </div>

                        <div v-if="loading" class="w-full text-center">
                            <Loader/>
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-black">Maison</label>
                            <Field class="form-control" name="house" placeholder="Maison"/>
                            <ErrorMessage class="form-invalid" name="house"/>
                        </div>
                    </div>

                    <button :disabled="submitting" class="btn-primary w-full mt-4">
                        <template v-if="submitting">...</template>
                        <template v-else>S'inscrire</template>
                    </button>
                </Form>
            </div>
        </ModalComponent>

        <div
            class="w-full text-center md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8">
            <span class="text-xl sm:text-4xl text-blue-900 font-bold">Confirmation des informations</span>

            <div class="card w-full flex flex-row mt-8 p-4">
                <table class="table">
                    <tr>
                        <td>Nom</td>
                        <th>{{ store.user_data.lastname }}</th>
                    </tr>
                    <tr>
                        <td>Prénoms</td>
                        <th>{{ store.user_data.firstname }}</th>
                    </tr>
                    <tr>
                        <td>Date de naissance</td>
                        <th>{{ store.user_data.birth_date }}</th>
                    </tr>
                    <tr>
                        <td>Lieu de naissance</td>
                        <th>{{ store.user_data.birth_place }}</th>
                    </tr>
                    <tr>
                        <td>Nationalité</td>
                        <th>{{ store.user_data.origin_country }}</th>
                    </tr>
                    <tr>
                        <td>Domicile</td>
                        <th>{{ store.user_data.address }}</th>
                    </tr>
                    <tr>
                        <td>Sexe</td>
                        <th>{{ store.user_data.gender }}</th>
                    </tr>
                    <tr>
                        <td>Numéro de téléphone</td>
                        <th>{{ store.user_data.telephone }}</th>
                    </tr>
                    <tr>
                        <td>Mail</td>
                        <th>{{ store.user_data.email }}</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="w-full text-center md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto">
            <span class="text-xl">Confirmez-vous les informations ci dessus? </span>

            <button class="btn-primary w-full mt-4" @click="open = !open">
                Je confirme
            </button>
        </div>
    </div>
</template>

<style lang="css" scoped>
</style>