<template>
	<LoaderSpinner v-if="loading || !order" type="block" class="mt-4" />

	<template v-else>
		<div class="intro-y flex items-center mt-4">
			<button class="btn bg-gray-400 text-white w-36 border-2" type="button" @click="$router.back()">
				<i class="fa-light fa-arrow-left w-4 h-4 mr-2"></i>
				Retour
			</button>
			<h2 class="text-lg font-semibold m-auto">Information de l'impression</h2>
		</div>

		<div v-if="order" class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary text-2xl">Service</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Type de service</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.demand.service.name }}</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Référence d'impression</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.reference }}</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Statut</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">
							<StatusComponent :status="order.status" :status-text="order.status_label" />
						</p>
					</div>
				</div>
				<div v-if="order.printed_at" class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Date d'impression</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.printed_at }}</p>
					</div>
				</div>
				<div v-if="order.received_at" class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Reçu par le propriétaire le :</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.received_at }}</p>
					</div>
				</div>
				<div v-if="order.printer" class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Imprimé par :</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">
							{{ order.printer.identity.fullName }}
						</p>
					</div>
				</div>
				<div v-if="order.validated_at" class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Validé le :</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.validated_at }}</p>
					</div>
				</div>
				<div v-if="order.rejected_at" class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Rejeté le :</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.rejected_at }}</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Observations :</p>
						<p class="font-bold text-danger w-1/2 text-lg leading-5">{{ order.observations }}</p>
					</div>
				</div>
			</div>

			<template v-if="order.plates.length > 0">
				<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
					<div class="intro-y flex items-center">
						<h2 class="text-primary text-2xl">Informations de plaque</h2>
					</div>
				</div>

				<div class="col-span-12 grid grid-cols-12 gap-6">
					<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Numéro Série Plaque 1</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ order.plates[0].serial_number }}
							</p>
						</div>
					</div>
					<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Numéro Série Plaque 2</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ order.plates[1].serial_number }}
							</p>
						</div>
					</div>
					<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">RFID Plaque 1</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ order.plates[0].rfid }}
							</p>
						</div>
					</div>
					<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">RFID Plaque 2</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ order.plates[1].rfid }}
							</p>
						</div>
					</div>
					<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Images</p>
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
			</template>

			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary text-2xl">Informations d'immatriculation</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
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
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
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

			<OwnerInfoCard :owner-info="owner.identity" />

			<VehicleInfoCard :vehicle-info="vehicle" />
		</div>
	</template>
</template>

<script setup>
	import { onBeforeUnmount, onMounted, ref } from "vue";
	import { storeToRefs } from "pinia";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { onBeforeRouteLeave } from "vue-router";
	import { usePrintOrderStore } from "@/stores/print-order.js";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import ImmatriculationPlateView from "@/components/immatriculation/ImmatriculationPlateView.vue";
	import StatusComponent from "@/components/StatusComponent.vue";

	const props = defineProps({
		id: String,
	});

	const orderStore = usePrintOrderStore();
	const { order, loading } = storeToRefs(orderStore);
	const owner = ref(null);
	const vehicle = ref(null);
	const immatriculation = ref(null);

	onMounted(async () => {
		await orderStore.fetchOrder(props.id).then((res) => {
			owner.value = res.immatriculation.vehicle.owner;
			immatriculation.value = res.immatriculation;
			vehicle.value = res.immatriculation.vehicle;
		});
	});

	onBeforeRouteLeave(() => {
		orderStore.clear();
	});

	onBeforeUnmount(() => {
		orderStore.clear();
	});
</script>
