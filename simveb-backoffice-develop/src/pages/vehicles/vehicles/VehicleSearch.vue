<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<div class="text-primary-dark text-2xl font-bold">Recherche globale sur un véhicule</div>
		</div>

		<div class="w-full my-4">
			<div class="flex">
				<button
					class="py-4 px-4 rounded-t-lg text-lg font-bold w-1/3 transition-colors"
					:class="{
						'text-gray-600 bg-white ': activeTab !== 'vin',
						'text-white bg-primary': activeTab === 'vin',
					}"
					@click="activeTab = 'vin'"
				>
					Recherche par VIN
				</button>
				<button
					class="py-4 px-4 rounded-t-lg text-lg font-bold w-1/3 transition-colors"
					:class="{
						'text-gray-600 bg-white ': activeTab !== 'immatriculation',
						'text-white bg-primary': activeTab === 'immatriculation',
					}"
					@click="activeTab = 'immatriculation'"
				>
					Recherche par Immatriculation
				</button>
			</div>
			<form class="bg-white shadow border-t-2 border-t-primary" @submit.prevent="handleSearch">
				<div class="relative">
					<input
						id="vin"
						v-model="vin"
						type="text"
						class="w-full border border-gray-300 p-4 focus:outline-none focus:ring-2 focus:ring-blue-60"
						:placeholder="activeTab === 'vin' ? 'Entrez le VIN' : 'Entrez l\'immatriculation'"
						required
						:class="{ 'border-red-500': errors.vin }"
					/>
					<button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400">
						<i class="fa fa-search"></i>
					</button>
				</div>
				<div v-if="errors.vin" class="text-red-500 text-sm">{{ errors.vin[0] }}</div>
				<div v-else-if="errors.immatriculation" class="text-red-500 text-sm">
					{{ errors.immatriculation[0] }}
				</div>
			</form>
		</div>

		<VPlaceloadWrap v-if="loading">
			<VPlaceload height="500px" class="mx-4" />
			<VPlaceload height="500px" class="mx-4" />
			<VPlaceload height="500px" class="mx-4" />
		</VPlaceloadWrap>

		<template v-else-if="vehicle">
			<div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4 tab-details-inner card-grid">
				<RelatedDemands v-if="can('browse-im-demand')" :demands="demands" />

				<VehicleInfoCard :vehicle="vehicle" />

				<VehicleLifecycle :demands="demands" />

				<VehicleAssets :passages="[]" />

				<PassageCard :passages="passages" />

				<InfractionCard :alerts="alerts" />
			</div>
		</template>
	</div>
</template>

<script setup lang="ts">
	import PassageCard from "/@src/pages/vehicles/Cards/PassageCard.vue";
	import VehicleLifecycle from "/@src/pages/vehicles/Cards/VehicleLifecycle.vue";
	import InfractionCard from "/@src/pages/vehicles/Cards/InfractionCard.vue";
	import RelatedDemands from "/@src/pages/vehicles/Cards/RelatedDemands.vue";
	import VehicleAssets from "/@src/pages/vehicles/Cards/VehicleAssets.vue";
	import client from "/@src/composable/axiosClient";
	import { userHasPermissions } from "/@src/utils/permission";
	import VehicleInfoCard from "/@src/pages/demands/Cards/VehicleInfoCard.vue";

	const vehicle = ref(null);
	const demands = ref([]);
	const alerts = ref([]);
	const passages = ref([]);
	const plates = ref([]);
	const actors = ref([]);
	const pledge = ref([]);
	const loading = ref(false);
	const route = useRoute();
	const errors = ref({});
	const activeTab = ref("vin");

	const vin = ref(route.params.vin);
	const { can } = userHasPermissions();

	const handleSearch = async () => {
		loading.value = true;
		await client({
			method: "GET",
			url: `admin/vehicles/vehicle-details?${activeTab.value}=${vin.value}`,
		})
			.then((response) => {
				passages.value = response.data.passages;
				vehicle.value = response.data.vehicle;
				demands.value = response.data.demands;
				alerts.value = response.data.alerts;
				plates.value = response.data.plates;
				actors.value = response.data.actors;
				pledge.value = response.data.pledge;
			})
			.catch((error) => {
				errors.value = error.response.data.errors;
			})
			.finally(() => {
				loading.value = false;
			});
	};

	onMounted(() => {
		if (vin.value) {
			handleSearch();
		}
	});
</script>

<style lang="scss">
	.card-grid {
		.r-card {
			height: 400px;
			overflow-y: scroll;
		}
		// design the scrollbar
		.r-card::-webkit-scrollbar {
			width: 5px;
		}
		.r-card::-webkit-scrollbar-thumb {
			background-color: var(--primary);
			border-radius: 10px;
		}
		.r-card::-webkit-scrollbar-track {
			background-color: var(--gray-200);
		}
	}
</style>
