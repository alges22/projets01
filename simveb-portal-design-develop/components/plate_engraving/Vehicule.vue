<script setup>
    import {useApi} from "~/helpers/useApi";
    import { usePlateEngraving } from "~/stores/plateEngraving";

    const api = useApi()

    const cars = ref([])

    const store = usePlateEngraving()

    const loading = ref(false)

    const userStore = useUserStore()

    const { car: selectedCar, vehicule_infos } = storeToRefs(store);

    const { user, online_profile } = storeToRefs(userStore)

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

    const getVehiculesInfos = (car) => {
        selectedCar.value = car

        api({
            method: 'GET',
            url: `/get-vehicle?vin=${car.vin}`
        })
            .then((response) => response.data)
            .then((response) => {
                vehicule_infos.value = response
            })
    }
</script>

<template>
    <div class="card w-full p-16 text-center mt-2">
        <div class="w-2/3 mx-auto">
            <span class="text-2xl text-blue-900 font-bold"> Choisissez le véhicule et passer à l'action</span>
            <div class="flex justify-center" v-if="loading">
                <Loader />
            </div>
            <div v-else class="text-left mt-8">
                <div class="flex flex-row text-primary border-b-2 text-sm p-4 font-medium mt-8">
                    <div class="w-1/5"></div>
                    <div class="w-2/5">Numéro de chassis</div>
                    <div class="w-1/5">Immatriculation</div>
                    <div class="w-1/5"></div>
                </div>
                <div>
                    <div
                        v-for="car in cars"
                        :key="car.id"
                        :class="{'row': true, 'active': selectedCar && selectedCar.id === car.id}"
                        class="flex flex-row text-gray-400 p-4 text-sm font-medium my-4 items-center">
                        <div class="w-1/5">
                            <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                        </div>
                        <div class="w-2/5"> {{ car.vin }} </div>
                        <div class="w-1/5">
                            <span class="rounded-md bg-gray-200 text-gray-500 font-bold px-4 py-2">
                                {{ car.immatriculation }}
                            </span>
                        </div>
                        <div class="w-1/5 flex justify-end">
                            <input
                                :checked="selectedCar && selectedCar.id === car.id"
                                class="rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6"
                                type="radio" @change="getVehiculesInfos(car)" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-blue" :disabled="!selectedCar" @click="store.nextStep()">Suivant</button>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
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