<template>
	<div>
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune commande trouvée"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #reference="{ item }"> {{ item.reference }}</template>
			<template #status_label="{ item }">
				<VTag :label="item.status" curved :color="statusColor(item?.status)" />
			</template>
			<template #amount="{ item }"> {{ item.amount }} XOF </template>
			<!-- <template #author="{ item }">
				<span>
					{{ item.vehicle_owner?.identity?.fullName }}
				</span>
			</template>-->

			<template #created_at="{ item }">
				{{ item.created_at }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-im-demand')"
						v-tooltip.right="'Voir'"
						color="primary"
						light
						icon="eye"
						:to="{
							name: 'orders_show',
							params: { orderId: item.id },
						}"
					/>
				</div>
			</template>

			<template #filters> </template>
		</DataTable>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import statusColor from "/@src/utils/status-color";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const getItems = async (metadata) => {
		url.value = "/admin-orders";
		await crudStore.fetchRows(metadata);
	};
	const options = ref({});
	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);
	const headers = [
		{ title: "Référence", key: "reference", sortable: true },
		{ title: "Statut", key: "status_label" },
		{ title: "Prix total", key: "amount" },
		// { title: "VIN", key: "vin" },
		// { title: "Type de service", key: "service" },
		// { title: "Initiateur", key: "author" },
		{ title: "Date", key: "created_at", sortable: true },
	];
</script>

<style lang="scss" scoped></style>
