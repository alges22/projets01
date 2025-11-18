<template>
	<template v-if="cartCount">
		<button
			v-tooltip
			class="mr-auto sm:mr-6 notification cursor-pointer"
			title="Panier"
			@click="cartModalOpen = true"
			@keydown.enter="cartModalOpen = true"
		>
			<i class="text-2xl fa-light fa-shopping-bag nav-icon" :class="cartCount ? 'fa-shake' : ''"></i>
			<span
				class="w-4 h-4 flex items-center justify-center absolute top-0 right-0 text-xs text-white rounded-full bg-danger font-medium -mt-1 -mr-1"
			>
				{{ cartCount }}
			</span>
		</button>

		<Teleport to="body">
			<Modal :show="cartModalOpen" size="modal-xl" @hidden="cartModalOpen = false">
				<ModalHeader class="pt-6">
					<h2 class="font-bold text-base mr-auto">Passer au paiement</h2>
					<button class="absolute right-0 top-0 mt-6 mr-3" @click="cartModalOpen = false">
						<i class="fa-solid fa-x w-8 h-4 font-bold" />
					</button>
				</ModalHeader>
				<ModalBody class="p-0">
					<LoaderSpinner v-if="loading" type="block" />
					<template v-else-if="cart">
						<section aria-labelledby="cart-heading">
							<h2 id="cart-heading" class="sr-only">Demandes dans votre panier</h2>
							<ul class="divide-y divide-gray-200 px-4 sm:px-6 lg:px-8">
								<li
									v-for="(item, index) in items"
									:key="index"
									class="flex py-8 text-sm sm:items-center"
								>
									<div
										class="ml-4 grid flex-auto grid-cols-1 grid-rows-1 items-start gap-y-3 gap-x-5 sm:ml-6 sm:flex sm:items-center sm:gap-0"
									>
										<div class="row-end-1 flex-auto sm:pr-6">
											<h3 class="font-medium text-gray-900">{{ item.service.name }}</h3>
											<p class="mt-1 text-gray-500">{{ item.reference }}</p>
										</div>
										<p
											class="row-span-2 row-end-2 font-medium text-gray-900 sm:order-1 sm:ml-6 sm:w-1/3 sm:flex-none sm:text-right"
										>
											{{ formatPrice(item.pivot.amount) }}
										</p>
										<div class="flex items-center sm:block sm:flex-none sm:text-center">
											<button
												type="button"
												class="ml-4 font-medium text-indigo-600 hover:text-indigo-500 sm:ml-0 sm:mt-2"
												@click="removeItem(item.id)"
											>
												<span>Retirer</span>
											</button>
										</div>
									</div>
								</li>
							</ul>
						</section>

						<section
							v-if="itemsCount > 0"
							aria-labelledby="summary-heading"
							class="mt-auto sm:px-6 lg:px-8"
						>
							<div class="bg-gray-50 p-6 sm:rounded-lg sm:p-8">
								<h2 id="summary-heading" class="sr-only">Récapitulatif de la commande</h2>

								<div class="flow-root">
									<dl class="-my-4 divide-y divide-gray-200 text-sm">
										<div class="flex items-center justify-between py-4">
											<dt class="text-base font-medium text-gray-900">Total</dt>
											<dd class="text-base font-medium text-gray-900">
												{{ formatPrice(cart.amount) }}
											</dd>
										</div>
									</dl>
								</div>
							</div>
						</section>

						<div v-else class="bg-gray-50 p-6 sm:rounded-lg sm:p-8">
							<h2 id="summary-heading" class="text-center">Votre panier est vide</h2>
						</div>
					</template>
				</ModalBody>
				<ModalFooter v-if="itemsCount > 0" class="">
					<div class="mt-10">
						<BasicButton
							type="submit"
							:loading="loading"
							class="w-full rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50"
							@click="validateCart"
						>
							Payer
						</BasicButton>
					</div>
					<div class="mt-6 text-center text-sm text-gray-500">
						<p>
							ou
							<button
								class="font-medium text-indigo-600 hover:text-indigo-500"
								@click="cartModalOpen = false"
							>
								Payer plus tard
								<span aria-hidden="true"> &rarr;</span>
							</button>
						</p>
					</div>

					<div v-if="services.length > 0" class="grid grid-cols-12 gap-6">
						<h4 class="col-span-12 font-semibold text-left text-base">
							Souhaitez-vous ajouter des services supplémentaires à votre panier avant de finaliser votre
							paiement ?
						</h4>
						<div v-for="(service, index) in services" :key="index" class="col-span-12 md:col-span-6">
							<ServiceCard
								:title="service.name"
								:to="{ name: serviceMap[service.code], params: { serviceId: service.id } }"
								:price="service.amount"
							/>
						</div>
					</div>
				</ModalFooter>
			</Modal>
		</Teleport>
	</template>
</template>

<script setup>
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import { useCartStore } from "@/stores/cart.js";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import ServiceCard from "@/components/ServiceCard.vue";
	import { computed, watch } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { formatPrice } from "../helpers/utils.js";
	import { launchFedapayCheckout } from "@/assets/js/fedapayCheckout.js";
	import { useRouter } from "vue-router";
	import Alert from "@/components/notification/alert.js";
	import BasicButton from "@/components/BasicButton.vue";
	import { serviceMap } from "../../space-config.js";

	const router = useRouter();
	const authStore = useAuthStore();
	const { online_profile, user } = storeToRefs(authStore);

	const cartStore = useCartStore();
	const { cartModalOpen, items, itemsCount, loading, cart, services } = storeToRefs(cartStore);

	const cartCount = computed(() => {
		return itemsCount.value ? itemsCount.value : online_profile.value.cart_demands;
	});

	const validateCart = () => {
		cartStore.validateCart().then((order) => {
			cartModalOpen.value = false;
			launchFedapayCheckout(order.reference, `Paiement de la commande ${order.reference}`, order.amount, {
				firstname: user.value.firstname,
				lastname: user.value.lastname,
				phone: user.value.telephone,
				email: user.value.email,
			})
				.then((transaction) => {
					submitOrder(transaction, order);
				})
				.catch(() => {
					Alert.error("Le paiement n'a pas abouti");
				});
		});
	};

	const removeItem = (id) => {
		cartStore.removeItem(id).then(() => {
			cartStore.fetchItems();
		});
	};

	const submitOrder = async (transaction, order) => {
		await cartStore.submitOrder(transaction.id.toString(), order.id).then(async () => {
			await router.push({ name: "payment_status", params: { orderId: order.id } });
		});
	};

	watch(cartModalOpen, (isOpen) => {
		if (isOpen) {
			cartStore.fetchItems();
		}
	});
</script>

<style scoped></style>
