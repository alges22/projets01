<template>
	<div class="intro-y mt-4 flex items-center col-span-12">
		<h2 class="mr-auto text-lg font-bold">Liste des véhicules recherchés</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
		<DataTable
			:headers="headers"
			:items="vehicles"
			:loading="loading"
			empty-text="Aucun véhicule trouvé"
			header-class="uppercase text-start"
			search
			:has-actions="false"
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #owner_name="{ item }">
				{{ `${item.vehicle_owner.lastname} ${item.vehicle_owner.firstname}` }}
			</template>
			<template #vehicle_provenance="{ item }">
				{{ item.vehicle_type?.value }}
			</template>
			<template #immatriculation_number="{ item }">
				<KeyField :value="item.vehicle.immatriculation_number" />
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.statut" />
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { onMounted, ref } from "vue";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "@/stores/crud.js";
	import KeyField from "@/components/KeyField.vue";

	const crudStore = useCrudStore();
	const { rows: vehicles, url, loading } = storeToRefs(crudStore);
	const options = ref({});

	const headers = [
		{ key: "immatriculation_number", title: "Immatriculation", sortable: false },
		{ key: "vehicle_provenance", title: "Véhicule", sortable: false },
		{ key: "owner_name", title: "Propriétaire", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
	];

	const fetchBlacklistedVehicles = async (metadata) => {
		url.value = "/blacklist-vehicles";
		await crudStore.fetchRows(metadata);
	};

	onMounted(async () => {
		await fetchBlacklistedVehicles(options.value);
	});
</script>

<style scoped></style>
