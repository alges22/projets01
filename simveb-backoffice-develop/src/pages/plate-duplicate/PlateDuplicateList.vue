<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useDemandStore } from "/@src/stores/modules/demand";
	import statusColor from "/@src/utils/status-color";

	const { can } = userHasPermissions();

	const duplicateStore = useDemandStore();
	const { demands, meta, loading, url } = storeToRefs(duplicateStore);
	const options = ref({});

	const getDemands = async (metadata) => {
		url.value = "plate-duplicates";
		await duplicateStore.fetchDemands(metadata);
	};

	const headers = [
		{ title: "Référence", key: "reference", sortable: true },
		{ title: "Nom du demandeur", key: "vehicle_owner_id", sortable: true },
		{ title: "Statut", key: "status_label" },
		{ title: "Date", key: "created_at", sortable: true },
	];

	watch(
		options,
		(newOptions) => {
			getDemands(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="demands"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune demande trouvée"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #vehicle_owner_id="{ item }">
				<span>
					{{ item.vehicle_owner?.identity?.fullName }}
				</span>
			</template>
			<template #vehicle_type_id="{ item }">
				<span>
					{{ `${item.immatriculation?.vehicle?.vehicle_type?.label}` }}
				</span>
			</template>
			<template #status_label="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item?.status)" />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-plate-duplicate')"
						v-tooltip.right="'Voir'"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'plate_duplicates_show',
							params: { id: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
