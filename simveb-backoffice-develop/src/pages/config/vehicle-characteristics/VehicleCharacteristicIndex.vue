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
			notyf.success("L'élément a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/vehicle-characteristics";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{
			title: "Catégorie de caractéristique",
			key: "category",
			sortable: true,
		},
		{ title: "Valeur", key: "value", sortable: true },
		{ title: "Minimum", key: "min_value", sortable: true },
		{ title: "Maximum", key: "max_value", sortable: true },
		{ title: "Coût", key: "cost", sortable: true },
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
			create-title="Ajout d'une caractéristique de véhicule"
			:create-link="can('store-vehicle-characteristic') ? { name: 'vehicle_characteristic_create' } : null"
			empty-text="Liste vide"
			search
			@create="true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #category="{ item }">
				{{ item.category?.label }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-vehicle-characteristic')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'vehicle_characteristic_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-vehicle-characteristic')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
