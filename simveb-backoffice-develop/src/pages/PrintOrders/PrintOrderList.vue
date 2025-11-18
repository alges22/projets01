<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="orders"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune impression trouvée"
			:create-button="false"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #reference="{ item }">
				<span>{{ item.demand.reference }}</span>
			</template>
			<template #immatriculation="{ item }">
				<span class="font-bold whitespace-nowrap">
					{{ item.immatriculation?.number_label }}
				</span>
			</template>
			<template #images="{ item }">
				<div class="flex flex-wrap gap-2">
					<template v-for="(image, index) in item.images" :key="index">
						<img
							height="50"
							width="50"
							:alt="'Image Impression N°' + index + 1"
							:src="image.url"
							data-action="zoom"
						/>
					</template>
				</div>
			</template>
			<template #status="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item.status)" />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-print-order')"
						v-tooltip.right="'Voir'"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'print_order_detail',
							params: { id: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { onUnmounted } from "vue";
	import statusColor from "/@src/utils/status-color";

	const { can } = userHasPermissions();

	const printStore = useCrudStore();
	const { rows: orders, meta, loading, url } = storeToRefs(printStore);
	const options = ref({});

	const headers = [
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "affected_at", title: "Date d'affectation", sortable: true },
		{ key: "status", title: "Statut", sortable: false },
		{ key: "images", title: "Images", sortable: true },
		{ key: "immatriculation", title: "immatriculation", sortable: true },
	];

	const getDemands = async (metadata) => {
		url.value = "print-orders";
		await printStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getDemands(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		orders.value = [];
		loading.value = false;
	});
</script>
