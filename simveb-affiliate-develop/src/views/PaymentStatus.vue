<template>
	<div class="justify-center flex mt-4">
		<div class="dashboard-card md:w-2/3 w-full lg:w-1/2 mx-auto">
			<LoaderSpinner v-if="loading || !order" />

			<template v-else-if="order?.transaction.status === 'approved'">
				<div class="p-5 text-center">
					<svg
						class="lucide lucide-check-circle w-24 h-24 text-success mx-auto mt-3"
						fill="none"
						height="32"
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						viewBox="0 0 24 24"
						width="32"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
					<div class="text-2xl mt-5 font-bold">Paiement réussi !</div>
					<div class="mt-2 text-base mb-4">
						Un email contenant les détails de votre commande a été envoyé à votre adresse email
						{{ user.email }}
					</div>
					<div class="mt-2 text-base mb-4">
						Référence : <span class="font-bold">{{ order.transaction.reference }}</span>
						<br />
						Montant : <span class="font-bold">{{ formatPrice(order.transaction.total_amount) }}</span>
					</div>

					<hr />

					<div class="px-5 pb-8 text-center mt-8">
						<BasicButton
							:loading="exportLoading"
							type="button"
							class="btn-outline-primary p-5 mx-2 text-base"
							@click="downloadInvoice"
						>
							Voir la facture
						</BasicButton>
					</div>

					<div class="mt-2 text-base">
						<p>Une question ou une préoccupation ?</p>
						<p>Veuillez nous contacter au XXXXXXXXXXXX</p>
					</div>
				</div>
			</template>

			<template v-else-if="order?.transaction.status === 'pending'">
				<div class="p-5 text-center">
					<i class="w-24 h-24 text-warning mx-auto mt-3 fa-solid fa-hourglass-half fa-spin text-8xl"> </i>
					<div class="text-2xl mt-5 font-bold">Paiement en attente !</div>
					<div class="mt-2 text-base mb-4">
						Votre paiement est en attente. Vous serez notifié quand elle sera validée
					</div>
				</div>
			</template>

			<template v-else>
				<div class="p-5 text-center">
					<i class="w-24 h-24 text-danger mx-auto mt-3 fa-solid fa-square-exclamation text-8xl"> </i>
					<div class="text-2xl mt-5 font-bold">Paiement échouée !</div>
					<div class="mt-2 text-base mb-4">
						Votre paiement a échoué. Veuillez réessayer ou contacter le support
					</div>
				</div>
			</template>
		</div>
	</div>
</template>

<script setup>
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted, ref } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { useCartStore } from "@/stores/cart.js";
	import { formatPrice } from "../helpers/utils.js";
	import { useTitle } from "@vueuse/core";
	import BasicButton from "@/components/BasicButton.vue";

	const { user } = storeToRefs(useAuthStore());
	const cartStore = useCartStore();
	const { order } = storeToRefs(cartStore);
	const loading = ref(true);
	const exportLoading = ref(false);

	const props = defineProps({
		orderId: {
			type: String,
			required: true,
		},
	});

	onBeforeMount(() => {
		loading.value = true;
	});

	const downloadInvoice = async () => {
		exportLoading.value = true;
		await cartStore
			.generateInvoice(order.value.id)
			.then((response) => {
				const href = URL.createObjectURL(response);
				const link = document.createElement("a");
				link.href = href;
				link.setAttribute("download", `Facture #${order.value.invoice.reference}.pdf`);
				document.body.appendChild(link);
				link.click();

				document.body.removeChild(link);
				URL.revokeObjectURL(href);
			})
			.finally(() => {
				exportLoading.value = false;
			});
	};

	onMounted(async () => {
		if (!order.value) {
			await cartStore.fetchOrder(props.orderId).then(() => {
				loading.value = false;
			});
		} else {
			loading.value = false;
		}
		switch (order.value.transaction.status) {
			case "approved":
				useTitle("Paiement réussi");
				break;
			case "pending":
				useTitle("Paiement en attente");
				break;
			default:
				useTitle("Paiement échouée");
				break;
		}
	});
</script>
