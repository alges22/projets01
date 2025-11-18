<template>
	<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
		<h2 class="text-lg leading-7 font-bold mb-8">Historique de passages</h2>
		<DataTable
			:has-actions="false"
			:has-header="false"
			:headers="headers"
			:items="history"
			empty-text="Aucun passage trouvé"
			header-class="uppercase text-start"
			search
		>
			<template #owner="{ item }">
				{{ `${item.vehicle_owner_lastname} ${item.vehicle_owner_firstname}` }}
			</template>
			<template #driver="{ item }">
				{{ `${item.driver_lastname} ${item.driver_firstname}` }}
			</template>
			<template #passage_type="{ item }">
				{{ item.passage_type.value }}
			</template>
		</DataTable>
	</div>
</template>
<script setup>
	const props = defineProps({
		id: {
			type: String,
			required: true,
		},
		loading: {
			type: Boolean,
			required: false,
			default: false,
		},
	});
	import DataTable from "@/components/DataTable.vue";
	import { onMounted } from "vue";
	import { storeToRefs } from "pinia";
	import { useVehiclePassageStore } from "@/stores/passage.js";

	const vehiclePassageStore = useVehiclePassageStore();
	const { history } = storeToRefs(vehiclePassageStore);

	const headers = [
		{ key: "immatriculation_number", title: "Immatriculation", sortable: false },
		{ key: "owner", title: "Propriétaire", sortable: false },
		{ key: "driver", title: "Conducteur", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "passage_type", title: "Passage", sortable: false },
	];

	onMounted(() => {
		vehiclePassageStore.fetchVehiclePassageHistory({ id: props.id });
	});
</script>
