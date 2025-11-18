<template>
	<DataTable
		:headers="headers"
		:items="demands"
		:loading="loading"
		:meta="meta"
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
		<template #created_at="{ item }">
			<span class="font-medium whitespace-nowrap">
				{{ dayjs(item.created_at).format("DD-MM-YYYY HH:mm") }}
			</span>
		</template>
		<template #actions="{ item }">
			<router-link
				v-if="can('show-im-demand')"
				:to="{ name: 'demand-show', params: { id: item.id } }"
				class="flex items-center mr-3 text-primary"
			>
				<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4" /> Voir la demande
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
	import { userHasPermissions } from "@/helpers/permissions.js";
	import { useDate } from "vue3-dayjs-plugin/useDate.js";

	const demandStore = useDemandStore();
	const { demands, loading, meta } = storeToRefs(demandStore);
	const { can } = userHasPermissions();

	const options = ref({});
	const dayjs = useDate();

	const headers = [
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: true },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const fetchDemands = async (metadata) => {
		await demandStore.fetchDemands(metadata);
	};

	watch(
		options,
		(newOptions) => {
			fetchDemands(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped></style>
