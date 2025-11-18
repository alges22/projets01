<script lang="ts" setup>
    import {useApi} from "~/helpers/useApi";
    import {useGrayCardDuplicateStore} from "~/stores/grayCardDuplicate";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    import {useRoute} from "vue-router";

    const api = useApi()

    const cars = ref([])

    const store = useGrayCardDuplicateStore()

    const loading = ref(false)

    const userStore = useUserStore()

    const {car: selectedCar, saving} = storeToRefs(store);

    const {user, online_profile} = storeToRefs(userStore)

    const isSpoiled = ref(false)

    const isLost = ref(false)

    const { $awn } = useNuxtApp()

    const schema = yup.object({
        vin: yup
            .string()
            .required("Veuillez sélectionner un véhicule"),

        is_spoiled: yup
            .bool()
            .default(false),

        is_lost: yup
            .bool()
            .default(false),

    }).test("exclusive", "Vous devez sélectionner soit 'Carte grise gâtée' soit 'Carte grise perdue'", (values) => {
        if (!values.is_spoiled && !values.is_lost) {
            return new yup.ValidationError(
                "Vous devez sélectionner au moins une option",
                null,
                "is_spoiled"
            );
        }
        if (values.is_spoiled && values.is_lost) {
            return new yup.ValidationError(
                "Vous ne pouvez pas sélectionner les deux options en même temps",
                null,
                "is_spoiled"
            );
        }
        return true;
    });


    onMounted(() => {
        loading.value = true

        const npiOrIfu = online_profile.value.type.code === 'company' ? online_profile.value.institution.ifu : user.value.identity.npi

        api({
            method: "GET",
            url: `/client/get-vehicles?key=${npiOrIfu}`,
        })
            .then((response) => response.data)
            .then(response => {
                cars.value = response
            })
            .catch((error) => {
                console.log(error.response.data.message)
            })
            .finally(() => {
                loading.value = false
            })
    })

    const route = useRoute()

    const id = route.params.id

    const onSubmit = (values) => {
        store.addToCart(id, user.value.identity.npi, {
            vin: values.vin,
            is_spoiled: values.is_spoiled,
            is_lost: values.is_lost
        }).then(() => {
            store.nextStep();
        }).catch((error) => {
            $awn.alert(error);
        });
    }
</script>

<template>
    <div class="card w-full p-16 text-center mt-2">
        <Form :validation-schema="schema" @submit="onSubmit">

            <div class="w-2/3 mx-auto">
                <span class="text-2xl text-blue-900 font-bold">Choisissez le véhicule</span>

                <div v-if="loading" class="flex justify-center">
                    <Loader/>
                </div>

                <div v-else class="text-left mt-8">
                    <div>
                        <Field v-model="selectedCar" class="hidden" name="vin" type="text" />

                        <div class="flex flex-row text-primary border-b-2 text-sm p-4 font-medium mt-8">
                            <div class="w-1/5"></div>
                            <div class="w-2/5">Numéro de chassis</div>
                            <div class="w-1/5">Immatriculation</div>
                            <div class="w-1/5"></div>
                        </div>

                        <div>
                            <div v-for="car in cars" :key="car.id"
                                 :class="{'row': true, 'active': selectedCar === car.vin}"
                                 class="flex flex-row text-gray-400 p-4 text-sm font-medium my-4 items-center">

                                <div class="w-1/5">
                                    <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                                </div>
                                <div class="w-2/5"> {{ car.vin }}</div>
                                <div class="w-1/5">
                            <span class="rounded-md bg-gray-200 text-gray-500 font-bold px-4 py-2">
                                {{ car.immatriculation }}
                            </span>
                                </div>
                                <div class="w-1/5 flex justify-end">
                                    <input v-model="selectedCar" :value="car.vin"
                                           class="rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6"
                                           type="radio" />
                                </div>
                            </div>
                        </div>

                        <ErrorMessage class="form-invalid" name="vin"/>
                    </div>

                    <div class="grid grid-cols-2 gap-8 text-left mt-8">
                        <div>
                            <Field v-model="isSpoiled" name="is_spoiled" type="hidden" />
                            <div class="shadow rounded p-8 border-2 flex cursor-pointer w-full"
                                 @click="isSpoiled = true; isLost = false">
                                <span> La carte grise est gâtée </span>
                                <input v-model="isSpoiled" :value="true" class="ms-auto rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6" type="radio"/>
                            </div>
                            <ErrorMessage class="form-invalid" name="is_spoiled" />
                        </div>

                        <div>
                            <Field v-model="isLost" name="is_lost" type="hidden" />
                            <div class="shadow rounded p-8 border-2 flex cursor-pointer w-full"
                                 @click="isLost = true; isSpoiled = false">
                                <span> La carte grise est perdue </span>
                                <input v-model="isLost" :value="true" class="ms-auto rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6" type="radio"/>
                            </div>
                            <ErrorMessage class="form-invalid" name="is_lost" />
                        </div>
                    </div>


                </div>

                <div class="text-right">
                    <button :disabled="saving" type="submit" class="btn btn-blue">Suivant</button>
                </div>
            </div>

        </Form>

    </div>
</template>

<style lang="scss" scoped>
/* Style for table rows */
tr {
    background-color: #f2f2f2;
    /* Alternate row background color */
    border-bottom: 1px solid #ddd;
    /* Border between rows */
}

/* Style for table cells */
td {
    padding: 10px;
    /* Add padding to cells for spacing */
}

.row.active {
    @apply bg-gray-50 rounded-xl shadow-md
}

.tab-container {
    @apply justify-center flex flex-row w-full mt-8
}

.tab {
    @apply text-gray-400 py-4 px-1 mx-4
}

.tab.active {
    @apply border-b-2 border-blue-600
}
</style>