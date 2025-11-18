<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("La forme de plaque a bien été supprimée");
			await getItems(options.value);
		});
	});

	const urlPath = "/plate-shapes";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Nom", key: "name" },
		{ title: "Cost", key: "cost", sortable: true },
		{ title: "Description", key: "description" },
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
			create-title="Ajout d'une forme de plaque"
			:create-link="can('store-plate-shape') ? { name: 'plate_shape_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-plate-shape')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'plate_shape_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-plate-shape')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
