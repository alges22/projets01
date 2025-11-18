<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();

	const urlPath = "/management-center-types";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le type de centre a bien été supprimé");
			await getItems(options.value);
		});
	});

	const headers = [
		{ title: "Libelle", key: "label" },
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
			create-title="Création d'un type de centre de gestion"
			:create-link="can('store-management-center-type') ? { name: 'management_center_types_create' } : null"
			empty-text="Aucun type de centre de gestion trouvé"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #commune="{ item }">
				{{ item.district?.name }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-management-center-type')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'management_center_types_create',
							params: { centerId: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-management-center-type')"
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
