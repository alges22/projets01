<template>
	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-outline-danger shadow-md mr-4" @click="alertTypeModalIsOpen = true">
				Signaler une voiture
			</button>
			<!--			<AddToBlacklistModal :vehicle-provenance="passage.vehicle_provenance?.name"></AddToBlacklistModal>-->
		</div>
	</div>

	<div class="intro-y box mt-8 rounded-lg">
		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<div v-if="passage.vehicle">
				<DetailVehicleInfoCard :vehicle-info="passage.vehicle" />
			</div>
		</div>

		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<div v-if="passage.vehicle_owner">
				<DetailOwnerInfoCard :owner-info="passage.vehicle_owner" />
			</div>
		</div>

		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<div v-if="passage.officer">
				<DetailOfficerInfoCard :officer-info="passage.officer" />
			</div>
		</div>

		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<div v-if="passage.vehicle">
				<PassageHistory :id="passage.vehicle.immatriculation_number" />
			</div>
		</div>
	</div>

	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-primary mr-4 border-2 w-48" type="button">Imprimer</button>
		</div>
	</div>

	<NewAlertForm
		:open="alertTypeModalIsOpen"
		:immatriculation-shown="false"
		@close="alertTypeModalIsOpen = false"
		@submit="alertTypeModalIsOpen = false"
	/>
</template>

<script setup>
	import { onMounted, ref } from "vue";
	import { storeToRefs } from "pinia";
	import DetailVehicleInfoCard from "@/views/Police/shared/DetailVehicleInfoCard.vue";
	import DetailOwnerInfoCard from "@/views/Police/shared/DetailOwnerInfoCard.vue";
	import DetailOfficerInfoCard from "@/views/Police/shared/DetailOfficerInfoCard.vue";
	import PassageHistory from "@/views/Police/shared/PassageHistory.vue";
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import NewAlertForm from "@/views/Police/Alerts/NewAlertForm.vue";
	import { useCrudStore } from "@/stores/crud.js";

	const vehiclePassageStore = useVehiclePassageStore();
	const { passage, loading, formLoading } = storeToRefs(vehiclePassageStore);
	const alertStore = useCrudStore();
	const { row: alert } = storeToRefs(alertStore);

	const props = defineProps({
		id: { type: String, required: true },
	});

	const alertTypeModalIsOpen = ref(false);

	onMounted(async () => {
		await vehiclePassageStore.fetchPassageDetail(props.id).then(() => {
			alert.value.immatriculation_number = passage.value.immatriculation_number;
		});
	});
</script>

<style scoped></style>
