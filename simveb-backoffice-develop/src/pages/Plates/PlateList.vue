<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="plates"
			:loading="loading"
			:has-actions="false"
			:meta="meta"
			create-title="Importer des plaques"
			empty-text="Aucune plaque trouvée"
			search
			:create-button="can('store-plate')"
			@create="importModal = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
			</template>
			<template #serial_number="{ item }">
				<KeyField :value="item.serial_number" />
			</template>
			<template #plate_color="{ item }">
				<div :style="{ backgroundColor: item.plate_color?.color_code }" class="color-box"></div>
			</template>
			<template #plate_shape="{ item }">
				{{ item.plate_shape.name }}
			</template>
			<template #in_anatt_stock="{ item }">
				<VIconBox v-if="item.in_stock" size="small" color="success" rounded>
					<i class="fa fa-check"></i>
				</VIconBox>
				<VIconBox v-else size="small" color="danger" rounded>
					<i class="fa-light fa-x" aria-hidden="true"></i>
				</VIconBox>
			</template>
			<template #in_affiliate_stock="{ item }">
				<VIconBox v-if="item.in_affiliate_stock" size="small" color="success" rounded>
					<i class="fa fa-check"></i>
				</VIconBox>
				<VIconBox v-else size="small" color="danger" rounded>
					<i class="fa-light fa-x" aria-hidden="true"></i>
				</VIconBox>
			</template>
		</DataTable>

		<PlateImport v-if="can('store-plate')" :open="importModal" @close="closeModal" @submit="importModal = false" />
	</div>
</template>

<script lang="ts" setup>
	import { storeToRefs } from "pinia";
	import dayjs from "dayjs";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { onUnmounted } from "vue";
	import { userHasPermissions } from "/@src/utils/permission";
	import PlateImport from "/@src/pages/Plates/PlateImport.vue";

	const plateStore = useCrudStore();
	const { rows: plates, meta, loading, url } = storeToRefs(plateStore);
	const { can } = userHasPermissions();
	const options = ref({});
	const importModal = ref(false);

	const headers = [
		{ title: "Numéro", key: "serial_number", sortable: true },
		{ title: "Forme", key: "plate_shape" },
		{ title: "Couleur", key: "plate_color" },
		{ title: "Date", key: "created_at", sortable: true },
	];

	const getPlates = async (metadata: Object) => {
		url.value = "plates";
		await plateStore.fetchRows(metadata);
	};

	const closeModal = async () => {
		importModal.value = false;
		await getPlates(options.value);
	};

	watch(
		options,
		(newOptions) => {
			getPlates(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		plates.value = [];
		loading.value = false;
	});
</script>

<style lang="scss" scoped>
	.color-box {
		width: 20px;
		height: 20px;
		border-radius: 50%;
	}
</style>
