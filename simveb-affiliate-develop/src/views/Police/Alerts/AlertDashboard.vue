<template>
	<template v-if="can('store-vehicle-alert')">
		<div class="intro-y flex items-center mt-4 lg:mt-16">
			<h2 class="text-lg font-semibold mr-auto">Signaler un véhicule</h2>
		</div>

		<div class="flex justify-between items-end dashboard-card !rounded-md mt-4">
			<div class="w-full">
				<TextInputGroup
					v-model="alert.immatriculation_number"
					name="immatriculation_number"
					class="w-full"
					label="Entrer le numéro d'immatriculation"
					:errors="errors.immatriculation_number"
				/>
			</div>
			<div class="ms-2 w-1/5">
				<BasicButton
					class="w-full h-auto btn-danger"
					:loading="loading"
					:disabled="loading || !alert.immatriculation_number"
					@click="alertTypeModalIsOpen = true"
				>
					Signaler
				</BasicButton>
			</div>
		</div>
	</template>

	<div class="intro-y mt-4 flex items-center col-span-12">
		<h2 class="mr-auto text-lg font-bold">Liste des véhicules en alerte</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 !rounded-none mt-4">
		<VehicleAlertList />
	</div>

	<NewAlertForm
		v-if="can('store-vehicle-alert')"
		:open="alertTypeModalIsOpen"
		:immatriculation-shown="false"
		@close="alertTypeModalIsOpen = false"
		@submit="alertTypeModalIsOpen = false"
	/>
</template>

<script setup>
	import { ref } from "vue";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { useCrudStore } from "@/stores/crud.js";
	import VehicleAlertList from "@/views/Police/Alerts/VehicleAlertList.vue";
	import NewAlertForm from "@/views/Police/Alerts/NewAlertForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
	const alertStore = useCrudStore();
	const { row: alert, loading, errors } = storeToRefs(alertStore);

	const alertTypeModalIsOpen = ref(false);
</script>
