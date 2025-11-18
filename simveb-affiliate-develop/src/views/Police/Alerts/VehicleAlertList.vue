<template>
	<DataTable
		:headers="headers"
		:items="alerts"
		empty-text="Aucune alerte trouvée"
		header-class="uppercase text-start"
		search
		:meta="meta"
		:create-button="false"
		@update-datatable="(newOptions) => (options = newOptions)"
	>
		<template #immatriculation_number="{ item }">
			<KeyField :value="item.vehicle.immatriculation" />
		</template>
		<template #owner="{ item }">
			{{ `${item.vehicle_owner.lastname} ${item.vehicle_owner.firstname}` }}
		</template>
		<template #driver="{ item }">
			{{ `${item.driver_lastname} ${item.driver_firstname}` }}
		</template>
	</DataTable>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { onMounted, ref, watch } from "vue";
	import DataTable from "@/components/DataTable.vue";
	import KeyField from "@/components/KeyField.vue";

	const alertStore = useCrudStore();
	const { rows: alerts, meta, loading, url } = storeToRefs(alertStore);
	const options = ref({});

	const headers = [
		{ key: "immatriculation_number", title: "Immatriculation", sortable: false },
		{ key: "owner", title: "Propriétaire", sortable: false },
		{ key: "driver", title: "Conducteur", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
	];

	onMounted(() => {
		loading.value = false;
	});

	const fetchAlerts = async (metadata) => {
		url.value = "vehicle-alerts";
		await alertStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			fetchAlerts(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>
