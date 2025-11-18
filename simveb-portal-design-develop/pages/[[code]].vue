<script setup>
    import {useServiceStore} from '../stores/service';
    import ModalComponent from "~/components/ModalComponent.vue";
    import {useApi} from "~/helpers/useApi";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    const route = useRoute();
    const code = route.params.code

    const serviceStore = useServiceStore()
    const { services, loading, service } = storeToRefs(serviceStore)

    onMounted(() => {
        serviceStore.getServices(code);
    })

    watch(() => route.params.code, (newCode) => {
        serviceStore.getServices(newCode);
    });

    const handleServiceClick = (service) => {
        if (service.children.length > 0) {
            navigateTo(service.code)
        }
    }
</script>

<template>
    <div v-if="loading" class="flex justify-center">
        <Loader/>
    </div>
    <div v-else class="px-2 my-4 md:gap-16 md:px-16 md:my-16">
        <div v-if="services.length === 0" class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3" role="alert">
            <p class="text-sm">Aucun service disponible</p>
        </div>
        <div v-if="code !== ''" class="gap-16 px-16 my-16">
            <RouterLink class="bg-white hover:bg-blue-900 hover:text-white text-blue-900 font-semibold
             py-2 px-4 border border-blue-900 rounded shadow" to="/" >
                <font-awesome-icon icon="fal fa-arrow-left" />
            </RouterLink>

            <span class="text-2xl font-bold text-blue-900 ml-4">{{ service?.name }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div v-for="service in services" :key="service.id"
                 class="bg-white shadow-md overflow-hidden rounded-lg">
                <div class="p-8 flex flex-col h-full">
                    <div class="flex-grow mb-4">
                        <h2 class="text-2xl font-bold text-blue-900">{{ service.name }}</h2>
                        <p class="text-gray-600  mt-4">{{ service.description }}</p>
                    </div>

                    <div class="py-2">
                        <RouterLink
                            :to="serviceStore.getLinksOfService(service)"
                            class="bg-white hover:bg-blue-900 hover:text-white text-blue-900 font-semibold py-2 px-4 border border-blue-900 rounded shadow"
                            @click="handleServiceClick(service)">
                            Faire une demande
                        </RouterLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>