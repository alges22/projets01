<script lang="ts" setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {useApi} from "~/helpers/useApi";
    import {storeToRefs} from "pinia";
    import {useServiceStore} from "~/stores/service";
    import {useSellStore} from "~/stores/myCarSell";
    import {VITE_SALE_DECLARATION} from "~/const";

    const isNavOpen = ref(false);
    const sellCar = ref("")

    const sellStore = useSellStore();
    let {service_id} = storeToRefs(sellStore)
    const serviceStore = useServiceStore()

    const userStore = useUserStore()
    const {permissions} = storeToRefs(userStore);

    const api = useApi();

    const toggleNavbar = () => {
        isNavOpen.value = !isNavOpen.value;
    }

    onMounted(() => {
        api({
            method: 'GET',
            url: `/client/services/${VITE_SALE_DECLARATION}`
        }).then((response) => response.data)
            .then((response) => {
                service_id.value = response.id

                sellCar.value = serviceStore.getLinksOfService(response)
            })
    })
</script>

<template>
    <div class="bg-blue-900 px-16">
        <nav class="menu flex flex-row">
            <ul :class="{ 'visible': isNavOpen }" class="menu-items-mobile">
                <FontAwesomeIcon class="text-white my-4 cursor-pointer" icon="fa fa-times" size="2xl"
                                 @click="toggleNavbar"/>
                <li :class="{ active: $route.path === '/' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/">
                        <font-awesome-icon icon="fal fa-home"/>
                        Accueil
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path.includes('/sales-declaration') }" class="menu-item"
                    @click="toggleNavbar">
                    <RouterLink :to="sellCar" active-class="active">
                        <font-awesome-icon icon="fal fa-hand-holding-dollar"/>
                        Faire une vente
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/purchases' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/purchases">
                        <font-awesome-icon icon="fal fa-cart-shopping"/>
                        Mes achats
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/my-cars' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/my-cars">
                        <font-awesome-icon icon="fal fa-car"/>
                        Mes véhicules
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/certificates' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/certificates">
                        <font-awesome-icon icon="fal fa-file"/>
                        Certificats
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/transactions' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/transactions">
                        <font-awesome-icon icon="fal fa-table-list"/>
                        Transactions
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/file-status' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/file-status">
                        <font-awesome-icon icon="fal fa-repeat"/>
                        Statut des dossiers
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/document-library' }" class="menu-item" @click="toggleNavbar">
                    <RouterLink active-class="active" to="/document-library">
                        <font-awesome-icon icon="fal fa-archive"/>
                        Documenthèque
                    </RouterLink>
                </li>
                <li v-if="permissions.includes('browse-space-staff')" :class="{ active: $route.path === '/membres' }"
                    class="menu-item"
                    @click="toggleNavbar">
                    <RouterLink active-class="active" to="/membres">
                        <font-awesome-icon icon="fal fa-users"/>
                        Membres
                    </RouterLink>
                </li>
            </ul>

            <ul class="menu-items">
                <li :class="{ active: $route.path === '/' }" class="menu-item">
                    <RouterLink active-class="active" to="/">
                        <font-awesome-icon icon="fal fa-home"/>
                        Accueil
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path.includes('/sales-declaration') }" class="menu-item">
                    <RouterLink :to="sellCar" active-class="active">
                        <font-awesome-icon icon="fal fa-hand-holding-dollar"/>
                        Faire une vente
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/purchases' }" class="menu-item">
                    <RouterLink active-class="active" to="/purchases">
                        <font-awesome-icon icon="fal fa-cart-shopping"/>
                        Mes achats
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/my-cars' }" class="menu-item">
                    <RouterLink active-class="active" to="/my-cars">
                        <font-awesome-icon icon="fal fa-car"/>
                        Mes véhicules
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/certificates' }" class="menu-item">
                    <RouterLink active-class="active" to="/certificates">
                        <font-awesome-icon icon="fal fa-file"/>
                        Certificats
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/transactions' }" class="menu-item">
                    <RouterLink active-class="active" to="/transactions">
                        <font-awesome-icon icon="fal fa-table-list"/>
                        Transactions
                    </RouterLink>
                </li>
                <li :class="{ active: $route.path === '/file-status' }" class="menu-item">
                    <RouterLink active-class="active" to="/file-status">
                        <font-awesome-icon icon="fal fa-repeat"/>
                        Statut des dossiers
                    </RouterLink>
                </li>
                <li v-if="permissions.includes('browse-space-staff')" :class="{ active: $route.path === '/membres' }"
                    class="menu-item">
                    <RouterLink active-class="active" to="/membres">
                        <font-awesome-icon icon="fal fa-users"/>
                        Membres
                    </RouterLink>
                </li>
            </ul>

            <div class="flex items-center justify-center cursor-pointer" style="width: 5%" @click="toggleNavbar">
                <font-awesome-icon :icon="['fal', 'bars-staggered']" size="2xl" style="color: #ffffff;"/>
            </div>
        </nav>
    </div>
</template>

<style scoped>
.menu-items {
    list-style-type: none;
    display: flex;
    justify-content: space-between;
    width: 95%;
}

@media screen and (max-width: 2000px) {
    .menu-items > .menu-item:nth-child(8) {
        display: none;
    }
}

@media screen and (max-width: 1700px) {
    .menu-items > .menu-item:nth-child(7) {
        display: none;
    }
}

@media screen and (max-width: 1400px) {
    .menu-items > .menu-item:nth-child(6) {
        display: none;
    }
}

@media screen and (max-width: 1100px) {
    .menu-items > .menu-item:nth-child(5) {
        display: none;
    }
}

@media screen and (max-width: 900px) {
    .menu-items > .menu-item:nth-child(4) {
        display: none;
    }
}

@media screen and (max-width: 600px) {
    .menu-items > .menu-item:nth-child(3) {
        display: none;
    }
}

@media screen and (max-width: 500px) {
    .menu-items > .menu-item:nth-child(2) {
        display: none;
    }
}

.menu-items-mobile {
    list-style-type: none;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    right: 0;
    z-index: 100001;
    height: 100vh;
    background-color: #1E3A8A;
    padding: 0;
    transition: transform 0.3s ease;
    transform: translateX(+100%);
}

.menu-items-mobile.visible {
    transform: translateX(0);
}

.menu-item {
    cursor: pointer;
    font-size: 19px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    padding: 20px;
    text-align: center;
    transition: background-color 0.2s ease-in-out;
}

.menu-item:hover {
    background-color: #0A55BC;
}

.menu-item > a > svg {
    padding-left: 5px;
    padding-right: 5px;
}

.menu-item.active {
    background-color: white;
    color: #1E3A8A;
    border-bottom: 3px solid #0A55BC;
}

.menu-item.active > a {
    color: #1E3A8A;
}

.menu-item a {
    text-decoration: none;
    color: white;
    font-weight: bold;
}
</style>