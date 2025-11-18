<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();
	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le service a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/anatt-service-types";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Code", key: "code" },
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Coût", key: "cost", sortable: true },
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
			create-title="Ajout d'un type de service de l'ANATT"
			:create-link="can('store-anatt-service-type') ? { name: 'anatt_service_type_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-anatt-service-type')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'anatt_service_type_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-anatt-service-type')"
						class="ml-2 has-text-danger"
						@click="handleDelete(item)"
						icon="trash"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
