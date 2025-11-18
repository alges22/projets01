<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();

	onBeforeMount(() => {
		url.value = "/management-centers";
	});

	const getItems = async (metadata) => {
		url.value = "/management-centers";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Titre du gérant", key: "manager_title", sortable: true },
		{ title: "Commune", key: "town", sortable: true },
		{ title: "Arrondissement", key: "district", sortable: true },
		{ title: "Quartier ou village", key: "village", sortable: true },
	];

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le centre a bien été supprimé");
			await getItems(options.value);
		});
	});

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
			create-title="Ajouter"
			:create-link="can('store-management-center') ? { name: 'management_centers_create' } : null"
			empty-text="Aucun centre de gestion trouvé"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #town="{ item }">
				{{ item.town?.name }}
			</template>
			<template #district="{ item }">
				{{ item.district?.name }}
			</template>
			<template #village="{ item }">
				{{ item.village?.name }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-management-center')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'management_centers_create',
							params: { centerId: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-management-center')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style scoped lang="scss"></style>
