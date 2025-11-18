<script lang="ts" setup>
    import { useApi } from "~/helpers/useApi";
    import { ref, onMounted, computed } from "vue";
    import {storeToRefs} from "pinia";
    import {useSellStore} from "~/stores/myCarSell";
    import {useServiceStore} from '~/stores/service';


    const api = useApi();
    const cars = ref([]);
    const currentPage = ref(1);
    const pageSize = 6;

    const sellStore = useSellStore();
    let { activeStep, service_id, car } = storeToRefs(sellStore)

    const userStore = useUserStore();
    let { user, online_profile } = storeToRefs(userStore)

    const serviceStore = useServiceStore()

    const router = useRouter();

    const loading = ref(true)

    onMounted(() => {
        fetchData();
    });

    const vendre = selectedCar => {

        api({
            method: 'GET',
            url: `/client/services/${import.meta.env.VITE_SALE_DECLARATION}`
        }).then((response) => response.data)
            .then((response) => {
                service_id.value = response.id
                activeStep.value = 1

                car.value = selectedCar

                let link = serviceStore.getLinksOfService(response)

                router.push(link);
            })
    }

    function fetchData() {
        const npiOrIfu = online_profile.value.type.code === 'company' ? online_profile.value.institution.ifu : user.value.identity.npi

        api({
            method: "GET",
            url: `/client/get-vehicles?key=${npiOrIfu}&page=${currentPage.value}&pageSize=${pageSize}`,
        }).then((response) => {
            cars.value = response.data

            loading.value = false
        }).catch ((error) => {
            console.log(error.response.data.message)
        })
    }

    function nextPage() {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
            fetchData();
        }
    }

    function prevPage() {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchData();
        }
    }

    function gotoPage(page) {
        currentPage.value = page;
        fetchData();
    }

    const totalPages = computed(() => Math.ceil(cars.value.length / pageSize));

    const displayedCars = computed(() => {
        const startIndex = (currentPage.value - 1) * pageSize;
        const endIndex = currentPage.value * pageSize;
        return cars.value.slice(startIndex, endIndex);
    });
</script>

<template>
    <div>
        <h4 class="text-center text-primary text-3xl font-bold mt-16">Mes véhicules</h4>
        <div v-if="loading" class="flex w-full justify-center mt-4">
            <Loader />
        </div>
        <div v-else-if="displayedCars.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 mt-8 md:px-8 xl:px-16 gap-4">
            <div v-for="car in displayedCars" :key="car.id" class="card p-8">
                <div class="flex-row flex items-center">
                    <div class="w-1/3">
                        <img alt="Véhicule" class="rounded-full" src="/vehicule.jpeg" style="width: 100px; height: 100px; object-fit: cover">
                    </div>
                    <div class="flex flex-col px-4 w-2/3">
                        <h3 class="text-primary font-bold">{{ car.immatriculation }}</h3>
                        <h6 class="text-xl text-gray-400 mt-1">{{ car.vin }}</h6>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <RouterLink class="bg-white text-blue-950 font-semibold py-2 px-8 mt-8 border border-blue-900 rounded shadow" :to="`/my-cars/${car.id}`">Voir les détails</RouterLink>
                    <button class="bg-white text-blue-950 font-semibold py-2 px-8 mt-8 border border-blue-900 rounded shadow" @click="vendre(car)">Vendre</button>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mt-4" role="alert">
                <strong class="font-bold">Aucun véhicule disponible</strong>
            </div>
        </div>

        <!-- Pagination controls -->
        <div v-if="displayedCars.length > 0" class="flex items-center gap-4 w-full justify-center mt-8">
            <button @click="prevPage" :disabled="currentPage === 1" class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray-900 uppercase align-middle transition-all rounded-lg select-none hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                Précédent
            </button>
            <div class="flex items-center gap-2">
                <button v-for="page in totalPages" :key="page" @click="gotoPage(page)" :class="{ 'bg-blue-900 text-white': page === currentPage, 'bg-white text-blue-900': page !== currentPage }" class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-lg text-center align-middle font-sans text-xs font-medium uppercase shadow-md shadow-gray-900/10 transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">{{ page }}</span>
                </button>
            </div>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray-900 uppercase align-middle transition-all rounded-lg select-none hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                Suivant
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>