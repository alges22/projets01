<script setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {useApi} from "~/helpers/useApi";
    import {launchFedapayCheckout} from "@/assets/js/fedapayCheckout.js";
    import {useUserStore} from "~/stores/user";
    import {useServiceStore} from "~/stores/service";
    import {userHasPermissions} from "~/helpers/permissions";

    const { $awn } = useNuxtApp()
    const api = useApi()
    const panier = ref(null)
    const loading = ref(true)
    const userStore = useUserStore()
    const {user, online_profile} = storeToRefs(userStore)
    const cartStore = useCartStore()

    const submitting = ref(false)

    const isEmptying = ref(false)

    const serviceStore = useServiceStore()

    const { can } = userHasPermissions()

    useHead({
        script: [
            {hid: 'Fedapay', src: 'https://cdn.fedapay.com/checkout.js?v=1.1.7', defer: true}
        ]
    })

    onMounted(() => {
        api({
            method: 'GET',
            url: '/client/cart'
        }).then((response) => response.data)
            .then(response => {
                panier.value = response
                loading.value = false
            })

        api({
            method : 'GET',
            url: '/current-user'
        }).then((response) => {
            userStore.setUser(response.data.user)
            userStore.setInvitations(response.data.invitations)
            userStore.setProfiles(response.data.user.profiles)
            userStore.setOnlineProfile(response.data.user.online_profile)
        })
    })

    const removeFromCart = (id) => {

        api({
            method: 'DELETE',
            url: `/client/cart-remove-demand/${id}`
        }).then((response) => response.data)
            .then(response => {
                panier.value = response
            })
            .finally(() => {

            })
    }

    const emptyCart = () => {
        isEmptying.value = true

        api({
            method: 'DELETE',
            url: '/client/empty-cart'
        }).then((response) => response.data)
            .then(response => {
                panier.value = response
            })
            .finally(() => {
                isEmptying.value = true
            })
    }

    const submitOrder = async (transaction, order) => {
        submitting.value = true

        await cartStore.submitOrder(transaction.id, order.id)
            .then(async (response) => {
                navigateTo(`/cart/checkout/${response.id}`);
            })
            .catch((error) => {
                $awn.alert(error)
            })
            .finally(() => {
                submitting.value = false
            })
    };

    const validateCart = () => {
        const url = can('approve-cart') ? '/approve-cart': '/client/validate-cart'

        cartStore
            .validateCart(url)
            .then((order) => {
                launchFedapayCheckout(order.reference, `Paiement de la commande ${order.reference}`, order.amount, {
                    firstname: user.value.identity.firstname,
                    lastname: user.value.identity.lastname,
                    phone: user.value.identity.telephone,
                    email: user.value.email,
                }).then((transaction) => {
                    submitOrder(transaction, order);
                }).catch(error => {
                    console.log(error)
                })
            })
            .catch(error => {
                $awn.alert(error)
            })
    };
</script>

<template>
    <template v-if="submitting">
        <Loader />

        <h4 class="text-blue font-bold text-xl text-center mb-4">Validation du panier en cours ... </h4>
    </template>

    <template v-else-if="loading">
        <Loader />
    </template>
    <div v-else>
        <h4 class="text-blue font-bold text-4xl text-center mb-4">Panier</h4>

        <div class="sm:py-8 sm:px-8 py-2 px-8 bg-green-200 mt-8 flex flex-col text-left rounded-2xl">
            <div class="flex-row flex px-8">
                <span class="font-bold text-2xl">Service</span>
                <span class="font-bold text-2xl ms-auto">Prix</span>
                <span class="px-2"></span>
            </div>
            <div v-for="(demande, index) in panier.demands" v-if="panier.demands.length > 0" :key="index" class="flex flex-row mt-4">
                <div class="px-8 py-4 bg-green-500 rounded-xl flex flex-row w-full">
                    <span class="text-white text-lg">{{ demande.service.name }} / {{ demande.reference }}</span>

                    <span class="text-white font-bold ms-auto text-xl"> {{ demande.pivot.amount }} FCFA</span>
                </div>
                <div class="flex items-center px-2">
                    <FontAwesomeIcon class="bg-red-500 p-2 text-white cursor-pointer" icon="fa fa-minus" @click="removeFromCart(demande.id)" />
                </div>
            </div>
            <div v-else class="px-8 py-4 bg-green-500 mt-4 rounded-xl flex flex-row">
                <span class="text-white text-lg"> Aucune donnée trouvée </span>
            </div>

            <hr class="bg-blue-500 h-px mt-16">

            <div class="flex flex-row">
            <span class="ms-auto text-2xl font-bold text-green-600">Total : {{
                    panier.amount.toLocaleString()
                }} FCFA</span>
            </div>
        </div>

        <div class="sm:w-3/3 xl:w-2/3 mx-auto mt-8" v-if="panier?.demands.length > 0">
            <h4 class="text-gray-600 text-2xl text-center">Souhaitez-vous ajouter des services supplémentaires
                <br> à votre panier avant de finaliser votre paiement ?</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                <div class="relative w-full p-12 group" v-for="service in panier.extra_services">
                    <div
                        class="absolute inset-0 w-full h-full bg-blue-100 border rounded-md border-blue-500 transition duration-300 flex px-8 items-center">
                        <font-awesome-icon :icon="['fas', 'circle-plus']" class="text-5xl text-blue-900"/>

                        <span
                            class="text-blue-900 font-bold text-left px-4">{{ service.name }}</span>
                    </div>
                    <RouterLink
                        :to="serviceStore.getLinksOfService(service)"
                        style="background-color: #0a53be"
                        class="absolute text-2xl font-bold inset-0 w-full h-full bg-transparent text-white px-4 py-2 rounded-md opacity-0 group-hover:opacity-100 transition duration-300">
                        <font-awesome-icon :icon="['fas', 'circle-plus']" class="text-white"/>
                        Ajouter
                    </RouterLink>
                </div>
            </div>
        </div>

        <div class="mt-8 flex-row flex">
            <button :disabled="panier?.demands.length === 0 || isEmptying" class="btn-red mx-4" @click="emptyCart">
                <span class="font-bold text-2xl">Vider le panier</span>
            </button>

            <div class="ms-auto">
                <button v-if="online_profile.type.code !== 'company' || can('approve-cart')" :disabled="panier?.demands.length === 0" class="btn-blue mx-4" @click="validateCart">
                    <span class="font-bold text-2xl">Payer</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>