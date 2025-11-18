<template>
	<div class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
		<VehicleInfoCard :vehicle-info="vehicleInfo" :loading="loading" />
	</div>
	<div class="intro-y col-span-12">
		<div class="flex align-center justify-end mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
				Annuler
			</button>
			<button class="btn btn-primary w-36" type="button" @click="$emit('next')">Suivant</button>
		</div>
	</div>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";

	defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { vehicle_info: vehicleInfo, loading } = storeToRefs(demandStore);
</script>

<style scoped></style>
