<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner la demande à mettre à jour</h2>
	</div>

	<DataTable
		:headers="headers"
		:items="demands"
		:loading="loading"
		:create-button="false"
		empty-text="Aucune demande trouvé"
		header-class="uppercase text-start"
		search
		@update-datatable="(newOptions) => (options = newOptions)"
	>
		<template #reference="{ item }">
			<span class="font-medium whitespace-nowrap">
				{{ item.reference }}
			</span>
			<div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
				{{ item.service }}
			</div>
		</template>
		<template #statut="{ item }">
			<StatusComponent :status="item.status" :status-text="item.status_label">
				<i v-if="item.status === 'in_cart'" class="fa-light fa-arrow-down-from-arc w-4 h-4 mr-2" />
			</StatusComponent>
		</template>
		<template #actions="{ item }">
			<router-link :to="goTo(item)" class="flex items-center mr-3 text-warning btn btn-outline-warning">
				<i class="fa-light fa-pencil w-4 h-4 mr-4" /> Modifier
			</router-link>
		</template>
	</DataTable>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { ref, watch } from "vue";
	import { updateServiceMap } from "../../../space-config.js";

	const demandStore = useDemandStore();
	const { demands, loading, loaded } = storeToRefs(demandStore);
	const options = ref({
		_no_paginate: 1,
		_updatable: 1,
	});

	const headers = [
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: true },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const fetchDemands = async (metadata) => {
		await demandStore.fetchDemands(metadata);
	};

	const goTo = (demand) => {
		return {
			name: updateServiceMap[demand.service_code],
			params: { serviceId: demand.service_id, demandId: demand.id },
		};
	};

	watch(
		options,
		(newOptions) => {
			fetchDemands(newOptions);
		},
		{ deep: true, immediate: !loaded.value }
	);
</script>
