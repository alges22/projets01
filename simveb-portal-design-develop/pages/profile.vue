<script setup>
    import {useApi} from "~/helpers/useApi";
    import {useUserStore} from "~/stores/user";
    import * as yup from "yup";
    import {ErrorMessage, Field, Form} from "vee-validate";

    useHead({
        title: 'SIMVEB - Profil',
    })

    const api = useApi()

    const userStore = useUserStore()

    const { user } = storeToRefs(userStore);

    const { $awn } = useNuxtApp()

    const data = ref({
        states : [],
        towns : [],
        districts : [],
    })

    const initial_values = {
        state_id : user.value.identity.state_id,
        district_id : user.value.identity.district_id,
        village_id : user.value.identity.village_id,
        town_id : user.value.identity.town_id,
        house : user.value.identity.house,
    }

    const submitting = ref(false)

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

    const updateTowns = ($event) => {
        const stateId = $event.target.value

        api({
            method: 'GET',
            url: `/registration/search/towns?state_id=${stateId}`
        }).then(response => response.data)
            .then(response => {
                data.value.towns = response
            })
    }

    const updateDistricts = ($event) => {
        const townId = $event.target.value

        api({
            method: 'GET',
            url: `/registration/search/districts?town_id=${townId}`
        }).then(response => response.data)
            .then(response => {
                data.value.districts = response
            })
    }

    const updateVillages = ($event) => {
        const villageId = $event.target.value

        api({
            method: 'GET',
            url: `/registration/search/villages?district_id=${villageId}`
        }).then(response => response.data)
            .then(response => {
                data.value.villages = response
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

    const onSubmit = (values) => {
        api({
            method: 'PUT',
            url: `/update-profile/${user.value.online_profile.id}`,
            data: values
        }).then(response => response.data)
            .then(response => {
                $awn.success('Modification effectuée avec succès')
            })
    }
</script>

<template>
    <div class="card p-4 mt-8 w-full mx-auto px-16 py-8">
        <div class="grid grid-cols-1 md:grid-cols-5">
            <div class="col-span-2">
                <div class="flex items-center">
                    <img alt="avatar" src="/avatar.png" style="width: 200px;">
                    <div class="flex flex-col ps-2">
                            <span class="font-bold text-sm sm:text-xl">
                            {{ user.identity.firstname }} {{ user.identity.lastname }}</span>
                        <span class="text-sm">NPI : {{ user.identity.npi }}</span>
                    </div>
                </div>
            </div>
            <div class="col-span-3">
                <Form :validation-schema="schema" @submit="onSubmit" :initial-values="initial_values">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-lg font-bold text-black">Département</label>
                            <Field name="state_id" as="select" class="form-control" @change="updateTowns">
                                <option value="">Selectionnez le département</option>
                                <option v-for="state in data.states" :value="state.id"> {{ state.name }} </option>
                            </Field>
                            <ErrorMessage name="state_id" class="form-invalid" />
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-black">Commune</label>
                            <Field name="town_id" as="select" class="form-control" @change="updateDistricts">
                                <option value="">Selectionnez la commune</option>
                                <option v-for="town in data.towns" :value="town.id"> {{ town.name }} </option>
                            </Field>
                            <ErrorMessage name="town_id" class="form-invalid" />
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-black">Arrondissements</label>
                            <Field name="district_id" as="select" class="form-control" @change="updateVillages">
                                <option value="">Selectionnez l'arrondissement</option>
                                <option v-for="district in data.districts" :value="district.id"> {{ district.name }} </option>
                            </Field>
                            <ErrorMessage name="district_id" class="form-invalid" />
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-black">Quartier ou village</label>
                            <Field name="village_id" as="select" class="form-control">
                                <option value="">Selectionnez le quartier ou village</option>
                                <option v-for="village in data.villages" :value="village.id"> {{ village.name }} </option>
                            </Field>
                            <ErrorMessage name="village_id" class="form-invalid" />
                        </div>

                        <div class="col-span-2">
                            <label class="block text-lg font-bold text-black">Maison</label>
                            <Field name="house"  class="form-control" placeholder="Maison" />
                            <ErrorMessage name="house" class="form-invalid" />
                        </div>
                    </div>

                    <button class="btn-primary w-full" :disabled="submitting">
                        <template v-if="submitting">...</template>
                        <template v-else>Mettre à jour</template>
                    </button>
                </Form>

            </div>
        </div>

    </div>
</template>

<style scoped>

</style>