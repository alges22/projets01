<template>
	<div class="content">
		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<AlertVehicleModal :registration-number="alert.vehicle?.immatriculation_number"></AlertVehicleModal>
				<AddToBlacklistModal :vehicle-provenance="'local'"></AddToBlacklistModal>
			</div>
		</div>

		<div class="intro-y box mt-8 rounded-lg">
			<div class="intro-y box mt-8 rounded-lg">
				<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
					<div v-if="alert.vehicle">
						<DetailVehicleInfoCard :vehicle-info="alert.vehicle" />
					</div>
				</div>

				<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
					<div v-if="alert.vehicle_owner">
						<DetailOwnerInfoCard :owner-info="alert.vehicle_owner" />
					</div>
				</div>

				<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
					<div v-if="alert.officer">
						<DetailOfficerInfoCard :officer-info="alert.officer" />
					</div>
				</div>

				<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
					<div v-if="alert.vehicle">
						<PassageHistory :id="alert.vehicle.immatriculation_number" />
					</div>
				</div>
			</div>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-primary mr-4 border-2 w-48" type="button">Imprimer</button>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { onMounted } from "vue";
	import { useRoute } from "vue-router";
	import { useAlertStore } from "../../../stores/alert.js";
	import { storeToRefs } from "pinia";

	import DetailVehicleInfoCard from "@/views/Police/shared/DetailVehicleInfoCard.vue";
	import DetailOwnerInfoCard from "@/views/Police/shared/DetailOwnerInfoCard.vue";
	import DetailOfficerInfoCard from "@/views/Police/shared/DetailOfficerInfoCard.vue";
	import PassageHistory from "@/views/Police/shared/PassageHistory.vue";
	import AlertVehicleModal from "@/views/Police/Alerts/AlertVehicleModal.vue";
	import AddToBlacklistModal from "@/views/Police/AddToBlacklistModal.vue";

	const alertStore = useAlertStore();
	const { alert } = storeToRefs(alertStore);

	const route = useRoute();
	const id = route.params.id;

	onMounted(() => {
		alertStore.fetchAlertDetail({ id: id });
	});
</script>

<style scoped></style>
