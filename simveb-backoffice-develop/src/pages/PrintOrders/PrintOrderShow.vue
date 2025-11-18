<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<div class="text-primary-dark text-2xl font-bold">Impression #{{ order?.reference }}</div>

			<div
				v-if="
					order.status == 'plate_printed' &&
					!order.validated_at &&
					!order.rejected_at &&
					can('validate-print')
				"
			>
				<VButton class="me-2" color="success" size="medium" raised @click="validationModal = true">
					Valider l'impression
				</VButton>
				<VButton color="danger" size="medium" raised @click="rejectPrint"> Rejeter l'impression </VButton>
			</div>
		</div>

		<VLoader size="large" :active="loading">
			<div class="grid grid-cols-2 xl:grid-cols-3 gap-4 tab-details-inner">
				<VCard class="side-card">
					<div class="card-inner is-one-third">
						<div class="columns">
							<div class="column">
								<span>Service</span>
							</div>
							<div class="column">
								<VTag :label="order.demand.service.name" color="primary" />
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Référence d'impression</span>
							</div>
							<div class="column">
								<span>{{ order.reference }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Statut</span>
							</div>
							<div class="column is-flex is-justify-content-space-between">
								<VTag :label="order.status_label" :color="statusColor(order?.status)" />
							</div>
						</div>
						<div v-if="order.printed_at" class="columns">
							<div class="column">
								<span>Date d'impression</span>
							</div>
							<div class="column">
								<span>{{ order.printed_at }}</span>
							</div>
						</div>
						<div v-if="order.printer" class="columns">
							<div class="column">
								<span>Imprimé par :</span>
							</div>
							<div class="column">
								<span>{{ order.printer.identity.fullName }}</span>
							</div>
						</div>
						<div v-if="order.received_at" class="columns">
							<div class="column">
								<span>Reçu par le propriétaire le :</span>
							</div>
							<div class="column">
								<span>{{ order.received_at }}</span>
							</div>
						</div>
						<template v-if="order.validated_at">
							<div class="columns">
								<div class="column">
									<span>Validé par :</span>
								</div>
								<div class="column">
									<span>{{ order.validator.identity.fullName }}</span>
								</div>
							</div>
							<div class="columns">
								<div class="column">
									<span>Validé le :</span>
								</div>
								<div class="column">
									<span>{{ order.validated_at }}</span>
								</div>
							</div>
						</template>
						<template v-if="order.rejected_at">
							<div class="columns">
								<div class="column">
									<span>Rejeté par :</span>
								</div>
								<div class="column">
									<span>{{ order.rejector.identity.fullName }}</span>
								</div>
							</div>
							<div class="columns">
								<div class="column">
									<span>Rejeté le :</span>
								</div>
								<div class="column">
									<span>{{ order.rejected_at }}</span>
								</div>
							</div>
						</template>
						<div class="columns">
							<div class="column">
								<span>Observations :</span>
							</div>
							<div class="column">
								<span>{{ order.observations }}</span>
							</div>
						</div>
					</div>
				</VCard>

				<VCard v-if="order.immmatriculation?.plates.length > 0" class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Informations de plaque</h3>
					</div>
					<div class="card-inner is-one-third">
						<div class="columns">
							<div class="column">
								<span>Numéro Série Plaque 1</span>
							</div>
							<div class="column">
								<span>{{ order.immatriculation?.plates[0].serial_number }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>RFID Plaque 1</span>
							</div>
							<div class="column">
								<span>{{ order.immatriculation?.plates[0].rfid }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Numéro Série Plaque 2</span>
							</div>
							<div class="column">
								<span>{{ order.immatriculation.plates[1].serial_number }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>RFID Plaque 2</span>
							</div>
							<div class="column">
								<span>{{ order.immatriculation.plates[1].rfid }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Images</span>
							</div>
							<div class="column">
								<div class="flex flex-wrap gap-2">
									<template v-for="(image, index) in order.images" :key="index">
										<img
											height="50"
											width="50"
											:alt="'Image Impression N°' + index + 1"
											:src="image.url"
											data-action="zoom"
										/>
									</template>
								</div>
							</div>
						</div>
					</div>
				</VCard>

				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Immatriculation</h3>
					</div>
					<div class="card-inner is-one-third">
						<div class="col-span-12 lg:col-span-6 m-2 lg:mx-9 intro-y">
							<p class="text-lg leading-5 mb-2 text-center">Avant</p>
							<ImmatriculationPlateView
								class="mx-auto"
								:bg-color="immatriculation.plate_color.color_code"
								:text-color="immatriculation.plate_color.text_color"
								:form="immatriculation.front_plate_shape.code"
								:immatriculation-number="immatriculation.label ?? immatriculation.number"
								:is-label="!!immatriculation.label"
								:country-code="immatriculation.country_code"
							/>
						</div>
						<div class="col-span-12 lg:col-span-6 m-2 lg:mx-9 intro-y">
							<p class="text-lg leading-5 mb-2 text-center">Arrière</p>
							<ImmatriculationPlateView
								class="mx-auto"
								:bg-color="immatriculation.plate_color.color_code"
								:text-color="immatriculation.plate_color.text_color"
								:form="immatriculation.back_plate_shape.code"
								:immatriculation-number="immatriculation.label ?? immatriculation.number"
								:is-label="!!immatriculation.label"
								:country-code="immatriculation.country_code"
							/>
						</div>
					</div>
				</VCard>

				<OwnerInfoCard :owner="owner.identity" title="Détails du propriétaire" />

				<VehicleInfoCard :vehicle="vehicle" />
			</div>
		</VLoader>

		<PrintOrderValidation
			v-if="order && !order.validated_at && can('validate-print')"
			:open="validationModal"
			:order="order"
			@close="validationModal = false"
			@submit="validationModal = false"
		/>
	</div>
</template>

<script setup lang="ts">
	import Swal from "sweetalert2/dist/sweetalert2.js";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { Notyf } from "notyf";
	import PrintOrderValidation from "/src/pages/demands/Actions/PrintOrderValidation.vue";
	import statusColor from "/@src/utils/status-color";
	import OwnerInfoCard from "/@src/pages/vehicles/Cards/OwnerInfoCard.vue";
	import VehicleInfoCard from "/@src/pages/demands/Cards/VehicleInfoCard.vue";

	const route = useRoute();
	const printStore = useCrudStore();
	const { row: order, url, loading } = storeToRefs(printStore);
	const owner = ref(null);
	const vehicle = ref(null);
	const immatriculation = ref(null);
	const notyf = new Notyf();
	const { can } = userHasPermissions();

	const orderId = route.params.id;
	const validationModal = ref(false);

	const rejectPrint = () => {
		url.value = "print-orders/validate-or-reject";
		Swal.fire({
			title: "Confirmation",
			text: "Vous êtes sur le point de rejeter cette impression?",
			icon: "warning",
			confirmButtonText: "Oui, Rejeter",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: "text",
			inputPlaceholder: "Pourquoi cet rejet ?",
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then((value) => {
			if (value.isConfirmed) {
				printStore
					.createRow({
						print_order_id: order.value.id,
						observations: value.value,
						action: "reject",
					})
					.then((data) => {
						notyf.success("l'impression a été rejetée !");
						url.value = "print-orders";
						printStore.fetchRow(order.value.id);
					})
					.catch(() => {});
			}
		});
	};

	onBeforeMount(() => {
		loading.value = true;
		url.value = "print-orders";
	});

	onMounted(async () => {
		await printStore.fetchRow(orderId).then((res) => {
			owner.value = res.immatriculation.vehicle.owner;
			immatriculation.value = res.immatriculation;
			vehicle.value = res.immatriculation.vehicle;
		});
	});

	onUnmounted(() => {
		order.value = null;
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-size-6-5 {
		font-size: 0.95rem;
		font-family: var(--font-alt);
	}
</style>
