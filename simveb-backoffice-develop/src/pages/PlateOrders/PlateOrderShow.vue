<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<BackButton />
			<!-- Dossier number -->
			<div class="text-primary-dark text-2xl font-bold w-1/2 text-ellipsis">Dossier #{{ order?.reference }}</div>

			<div>
				<button
					v-if="order && order.status === 'pending' && can('validate-plate-order')"
					type="button"
					class="bg-success-600 w-36 text-white text-xl px-4 py-2 rounded-full"
					:disabled="formLoading"
					@click="validationModal = true"
				>
					<i v-if="formLoading" class="fa-light fa-spinner-third animate-spin" />
					Valider
				</button>

				<button
					v-if="order && order.status === 'pending' && can('reject-plate-order')"
					type="button"
					class="bg-danger-600 w-36 text-white text-xl px-4 py-2 rounded-full"
					:disabled="formLoading"
					@click="rejectOrder"
				>
					<i v-if="formLoading" class="fa-light fa-spinner-third animate-spin" />
					Rejeter
				</button>
			</div>
		</div>
		<VLoader :active="loading" size="large">
			<VCard class="side-card">
				<div class="card-head">
					<h3 class="font-bold text-light">Détails de la commande</h3>
				</div>
				<div class="card-inner is-one-third">
					<div class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Affilié </span>
						</div>
						<div class="column">
							<span class="">
								{{ order.buyer.social_reason }}
							</span>
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span class="has-text-weight-semibold">Référence</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							<div>
								<span>{{ order.reference }}</span>
							</div>
						</div>
					</div>

					<div class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Date de création </span>
						</div>
						<div class="column">
							<span class="">{{ dayjs(order.created_at).format("DD/MM/YYYY H:m") }}</span>
						</div>
					</div>

					<div class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Montant </span>
						</div>
						<div class="column">
							<span class="">{{ formatPrice(order.amount) }}</span>
						</div>
					</div>

					<div v-if="order.confirmed_at" class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Confirmé par </span>
						</div>
						<div class="column">
							<span class="">
								{{ order.confirmator.identity.fullName }}
							</span>
						</div>
					</div>

					<div v-if="order.validated_at" class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Validé par </span>
						</div>
						<div class="column">
							<span class="">
								<!--									{{ order.validator.identity.fullName }}-->
							</span>
						</div>
					</div>

					<div v-if="order.rejected_at" class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold"> Rejeté par </span>
						</div>
						<div class="column">
							<span class="">
								<!--									{{ order.rejector.identity.fullName }}-->
							</span>
						</div>
					</div>

					<div class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Status du paiement </span>
						</div>
						<div class="column">
							<VTag :color="statusColor(order?.status)" :label="order?.status_label" />
						</div>
					</div>

					<div v-if="order.paid_at" class="columns">
						<div class="column">
							<span class="has-text-weight-semibold"> Date de paiement </span>
						</div>
						<div class="column">
							<span class="">
								{{ dayjs(order.paid_at).format("DD/MM/YYYY") }}
							</span>
						</div>
					</div>
				</div>

				<div class="tab-details-inner">
					<div class="card-head bg-primary-100 mt-8">
						<h3 class="!text-primary-dark">Plaques commandées</h3>
					</div>

					<div class="card-inner is-one-third">
						<DataTable
							:headers="[
								{ title: 'Forme', key: 'shape' },
								{ title: 'Couleur', key: 'color' },
								{ title: 'Quantité', key: 'nb' },
								{ title: 'Montant unité', key: 'unity_amount' },
								{ title: 'Total', key: 'total_amount' },
							]"
							:has-actions="false"
							:items="order.order_details"
							:has-header="false"
						>
							<template #unity_amount="{ item }">
								{{ formatPrice(item.unity_amount) }}
							</template>
							<template #total_amount="{ item }">
								{{ formatPrice(item.total_amount) }}
							</template>
						</DataTable>
					</div>
				</div>
			</VCard>
		</VLoader>

		<PlateOrderValidation
			v-if="order && can('validate-plate-order')"
			:open="validationModal"
			:order="order"
			@close="validationModal = false"
			@submit="submitValidation"
		/>
	</div>
</template>

<script lang="ts" setup>
	import Swal from "sweetalert2/dist/sweetalert2.js";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { Notyf } from "notyf";
	import PlateOrderValidation from "/@src/pages/PlateOrders/PlateOrderValidation.vue";
	import statusColor from "/@src/utils/status-color";
	import dayjs from "dayjs";
	import { usePriceFormat } from "/@src/composable/priceFormat";
	import BackButton from "/@src/pages/activity-logs/BackButton.vue";

	const route = useRoute();
	const crudStore = useCrudStore();
	const { row: order, url, loading, formLoading } = storeToRefs(crudStore);
	const notyf = new Notyf();
	const { can } = userHasPermissions();
	const { formatPrice } = usePriceFormat();
	const validationModal = ref(false);
	const orderDetails = ref([]);

	const orderId = route.params.id;

	const submitValidation = () => {
		validationModal.value = false;

		crudStore.fetchRow(orderId).then((response) => {
			orderDetails.value = response;
		});
	};

	const rejectOrder = () => {
		url.value = "plate-orders/reject";
		Swal.fire({
			title: "Confirmation",
			text: "Êtes vous sûr(e) de vouloir rejeter cette commande?",
			icon: "warning",
			confirmButtonText: "Oui, Rejeter",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: "text",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then((value) => {
			if (value.isConfirmed) {
				crudStore
					.createRow({
						plate_order_id: order.value.id,
						reason: value.value,
					})
					.then(() => {
						notyf.success("Commande rejetée avec succès !");
						url.value = "plate-orders";
						crudStore.fetchRow(order.value.id);
					})
					.catch(() => {});
			}
		});
	};

	onBeforeMount(() => {
		loading.value = true;
		url.value = "plate-orders";
	});

	onMounted(async () => {
		crudStore.fetchRow(orderId).then((response) => {
			orderDetails.value = response;
		});
	});

	onUnmounted(() => {
		order.value = null;
		orderDetails.value = [];
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-size-6-5 {
		font-size: 0.95rem;
		font-family: var(--font-alt);
	}
</style>
