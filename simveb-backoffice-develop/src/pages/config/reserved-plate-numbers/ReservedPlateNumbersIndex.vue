<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import dayjs from "dayjs";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import statusColor from "/@src/utils/status-color";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("L'élément a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/reserved-plate-numbers";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "label alphabetic", key: "alphabetic_label" },
		{ title: "label numeric", key: "numeric_label" },
		{ title: "Numéro minimum", key: "min" },
		{ title: "Numéro maximum", key: "max" },
		{ title: "status", key: "status" },
		{ title: "Date de création", key: "created_at", sortable: true },
	];

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		crudStore.reset();
	});
</script>

<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Faire une réservation de plaques"
			:create-link="can('store-reserved-plate-number') ? { name: 'reserved-plate-numbers_creation' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}
			</template>
			<template #status="{ item }">
				<VTag :label="item.status" :color="statusColor(item?.status)"></VTag>
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-reserved-plate-number')"
						v-tooltip="'Modifier un type d\'immatriculation'"
						class="ml-2 has-text-primary"
						icon="pen"
						:to="{
							name: 'reserved-plate-numbers_creation',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('show-reserved-plate-number')"
						v-tooltip="'Détails'"
						class="ml-2 has-text-primary"
						icon="eye"
						:to="{
							name: 'reserved-plate-numbers_show',
							params: { id: item.id },
						}"
					/>

					<VIconButton
						v-if="can('delete-reserved-plate-number')"
						v-tooltip="'Supprimer'"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
