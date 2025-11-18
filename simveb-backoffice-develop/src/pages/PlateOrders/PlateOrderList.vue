<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="orders"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune commande trouvée"
			create-title="Exporter"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #buttons>
				<VButton
					v-if="can('browse-plate')"
					size="medium"
					icon="rectangle-history-circle-plus"
					color="primary"
					raised
					:to="{ name: 'plate_list' }"
				>
					Inventaire des plaques
				</VButton>
			</template>
			<template #reference="{ item }">
				<KeyField
					:value="item.reference"
					:to="{
						name: 'plate_order_detail',
						params: { id: item.id },
					}"
				/>
			</template>
			<template #buyer="{ item }">
				<span class="text-ellipsis">{{ item.buyer.name }}</span>
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
			</template>
			<template #status_label="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item.status)" />
			</template>
			<template #amount="{ item }">
				{{ formatPrice(item.amount) }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-plate-order')"
						v-tooltip.right="'Voir'"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'plate_order_detail',
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
	import dayjs from "dayjs";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { onUnmounted } from "vue";
	import { userHasPermissions } from "/@src/utils/permission";
	import statusColor from "/@src/utils/status-color";
	import { usePriceFormat } from "/@src/composable/priceFormat";

	const orderStore = useCrudStore();
	const { rows: orders, meta, loading, url } = storeToRefs(orderStore);
	const { can } = userHasPermissions();
	const options = ref({});
	const { formatPrice } = usePriceFormat();

	const headers = [
		{ title: "Référence", key: "reference", sortable: true },
		{ title: "Agrée", key: "buyer", sortable: true },
		{ title: "Statut", key: "status_label" },
		{ title: "Montant", key: "amount", sortable: true },
		{ title: "Date", key: "created_at", sortable: true },
	];

	const getOrders = async (metadata: Object) => {
		url.value = "plate-orders/requests";
		await orderStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getOrders(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		orders.value = [];
		loading.value = false;
	});
</script>
