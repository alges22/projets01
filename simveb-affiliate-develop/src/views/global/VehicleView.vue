<template>
	<div class="intro-y flex items-center mt-4"></div>

	<LoaderSpinner v-if="loading" type="block" />

	<div v-else class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
		<VehicleInfoCard :vehicle-info="vehicle" :loading="loading" />

		<template v-if="plates.length > 0">
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
							{{ plates[0].serial_number }}
						</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Numéro Série Plaque 2</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">
							{{ plates[1].serial_number }}
						</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">RFID Plaque 1</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">
							{{ plates[0].rfid }}
						</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">RFID Plaque 2</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">
							{{ plates[1].rfid }}
						</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Images</p>
						<div class="flex flex-wrap gap-2">
							<template v-for="(image, index) in vehicle.images" :key="index">
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
	</div>

	<div class="intro-y col-span-12">
		<div class="flex align-center justify-end mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border" type="button" @click="$router.back()">
				Retour
			</button>
		</div>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted, ref } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { useVehicleStore } from "@/stores/vehicle.js";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import ImmatriculationPlateView from "@/components/immatriculation/ImmatriculationPlateView.vue";

	const vehicleStore = useVehicleStore();
	const { vehicle, loading, url } = storeToRefs(vehicleStore);
	const plates = ref([]);
	const owner = ref(null);
	const immatriculation = ref(null);

	const props = defineProps({
		vin: {
			type: String,
			required: true,
		},
	});

	onBeforeMount(() => {
		loading.value = true;
		vehicle.value = null;
	});

	const fetchVehicle = async () => {
		url.value = `vehicles`;
		await vehicleStore.searchVehicle(props.vin).then((res) => {
			vehicle.value = res.vehicle;
			owner.value = res.owner;
			immatriculation.value = res.immatriculation;
			plates.value = res.plates;
		});
	};

	onMounted(async () => {
		await fetchVehicle();
	});
</script>

<style scoped></style>
