<template>
    <div>
        <div v-if="view == 'allCars'">
            <div v-if="loading" class="text-center">
                <Loader />
            </div>
            <div v-else>
                <div v-if="allCars.length == 0">
                    <p class="text-blue-900 text-center text-xl">Pas de voitures</p>
                </div>
                <div v-else>
                    <h4 class="text-center text-primary text-3xl font-bold">Mes véhicules</h4>
                    <div class="grid grid-cols-3 gap-16 mt-8">
                        <div class="card p-10" v-for="car in allCars" :key="car.id">
                            <div class="flex-row flex items-center">
                                <div>
                                    <img alt="Véhicule" class="rounded-full" src="/vehicule.jpeg"
                                        style="width: 120px; height: 120px; object-fit: cover">
                                </div>
                                <div class="flex flex-col px-4">
                                    <h4 class="text-primary text-3xl font-bold">{{ car.vehicle_model }}</h4>
                                    <h6 class="text-xl text-gray-400 mt-1">{{ car.vin }}</h6>
                                </div>
                            </div>
    
                            <div class="flex flex-row justify-between">
                                <button class="btn-outline-primary" @click="viewCar(car)">Voir les
                                    détails</button>
                                <button class="btn-outline-primary"
                                    @click="store.setCar(car); store.setStep('')">Vendre</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div v-else class="bg-white p-10 rounded-lg">
            <VehiculeDetails :car="car" @close-details="viewAllCars()" @vendre-event="store.setCar(car); store.setStep('')"/>
        </div>
    </div>
</template>

<script setup>
    import VehiculeDetails from "./VehiculeDetails.vue"
    import {useSellStore} from "~/stores/myCarSell"
    const {$awn} = useNuxtApp()
    const store = useSellStore()
    onBeforeMount(()=>{
        store.chargeCar()
        .then(()=>{
            loading.value = false
        })
        .catch((error)=>{
            $awn.alert(error)
        })
    })
    let {car, allCars, loading} = storeToRefs(store)
    const view = ref('allCars');
    const viewCar = (_car) => {
        store.setCar(_car)
        view.value = ''
    }
    const viewAllCars = () =>{
        view.value = 'allCars'
    }
</script>

<style lang="css" scoped>

</style>