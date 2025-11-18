<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button class="btn btn-outline-danger shadow-md mr-4" @click="alertTypeModalIsOpen = true">
				Signaler une voiture
			</button>
			<router-link class="btn btn-primary shadow-md mr-2" :to="{ name: 'register-vehicle' }">
				Enregistrer une voiture
			</router-link>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12">
			<div class="grid grid-cols-12 gap-6">
				<DashboardStats />
			</div>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-4">
		<div class="intro-y pt-4 flex items-center col-span-12">
			<h2 class="mr-auto text-lg font-bold">Activité Récentes</h2>
		</div>
		<DataTable
			:headers="headers"
			:items="passages"
			empty-text="Aucun passage trouvé"
			header-class="uppercase text-start"
			search
			:meta="meta"
			:create-button="false"
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #statut="{ item }">
				<StatusComponent :status="item.statut" />
			</template>
			<template #driver="{ item }">
				{{ `${item.driver_lastname} ${item.driver_firstname}` }}
			</template>
			<template #owner_name="{ item }">
				{{ `${item.vehicle_owner_lastname} ${item.vehicle_owner_firstname}` }}
			</template>
			<template #actions="{ item }">
				<router-link :to="{ name: 'vehicle-detail', params: { id: item?.id } }">Voir détails</router-link>
			</template>

			<template #vehicle_provenance="{ item }">
				{{ item.vehicle_provenance.value }}
			</template>
		</DataTable>
	</div>

	<NewAlertForm
		:open="alertTypeModalIsOpen"
		@close="alertTypeModalIsOpen = false"
		@submit="alertTypeModalIsOpen = false"
	/>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { ref, watch } from "vue";
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import { storeToRefs } from "pinia";
	import NewAlertForm from "@/views/Police/Alerts/NewAlertForm.vue";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const vehiclePassageStore = useVehiclePassageStore();
	const { passages, meta } = storeToRefs(vehiclePassageStore);
	const options = ref({});
	const alertTypeModalIsOpen = ref(false);

	const headers = [
		{ key: "immatriculation_number", title: "Immatriculation", sortable: false },
		{ key: "vehicle_provenance_label", title: "Véhicule", sortable: false },
		{ key: "owner_name", title: "Propriétaire", sortable: false },
		{ key: "driver", title: "Conducteur", sortable: false },
		{ key: "created_date", title: "Date et heure", sortable: false },
		{ key: "passage_type_label", title: "Passage", sortable: false },
	];

	const fetchPassages = async (metadata) => {
		await vehiclePassageStore.fetchPassages(metadata);
	};
	watch(
		options,
		(newOptions) => {
			fetchPassages(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped></style>
