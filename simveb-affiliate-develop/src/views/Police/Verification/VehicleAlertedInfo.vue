<template>
	<div class="col-span-12 mt-4">
		<Alert
			v-if="!vehicleInfo.is_alerted"
			class="alert-outline-success bg-green-50 flex items-center mb-2 font-bold"
		>
			<i class="fa-solid text-xl fa-check-circle w-6 h-6 mr-4" />
			<span class="text-dark">Ce véhicule est en règle</span>
		</Alert>
		<Alert v-else class="alert-outline-danger bg-red-50 flex flex-col mb-2 font-bold">
			<div class="w-full text-center">
				<i class="fa-solid fa-4x fa-triangle-exclamation w-12 h-12 mr-4" />
			</div>
			<span class="text-dark text-center mt-2 font-bold text-xl">Ce véhicule est en alerte</span>
		</Alert>
	</div>

	<template v-if="!vehicleInfo.is_alerted">
		<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
			<VehicleInfoCard :vehicle-info="vehicleInfo.vehicle" :loading="loading" />

			<OwnerInfoCard :owner-info="vehicleInfo.vehicle_owner" />
		</div>

		<slot name="buttons"></slot>
	</template>

	<template v-else>
		<div class="grid grid-cols-2 gap-4 mt-4">
			<div
				v-for="(alertType, index) in vehicleInfo.alert_types"
				:key="index"
				class="col-span-2 lg:col-span-1 flex flex-col lg:flex-row items-center p-2 border-danger border rounded-md bg-red-50"
			>
				<div class="w-24 h-24 lg:w-12 lg:h-12 lg:mr-4">
					<img :alt="alertType.description" class="" :src="alertType.image" />
					<div class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5">
						<i class="fa-light fa-check"></i>
					</div>
				</div>
				<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
					<h2 class="font-semibold">{{ alertType.name }}</h2>
				</div>
				<div class="ml-auto mr-4 text-center lg:text-left mt-3 lg:mt-0">
					<button class="btn btn-outline-dark bg-white">Voir Détails</button>
				</div>
			</div>
		</div>
		<SoftVehicleInfo
			:vehicle-info="vehicleInfo.vehicle"
			:vehicle-owner="vehicleInfo.vehicle_owner"
			color="danger"
		/>
		<slot name="alerted-buttons"></slot>
	</template>
</template>
<script setup>
	import SoftVehicleInfo from "@/components/SoftVehicleInfo.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import Alert from "@/global-components/alert/index.js";

	defineProps({
		vehicleInfo: {
			type: Object,
			required: true,
		},
	});
</script>
