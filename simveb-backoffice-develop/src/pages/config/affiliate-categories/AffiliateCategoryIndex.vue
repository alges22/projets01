<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const { can } = userHasPermissions();
	const notyf = new Notyf();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("La catégorie d'affiliée a bien été supprimée");
			await getItems(options.value);
		});
	});

	const urlPath = "/affiliate-categories";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Libellé", key: "label", sortable: true },
		{ title: "Nom", key: "name", sortable: true },
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
			create-title="Ajout d'une catégorie"
			:create-link="can('store-affiliate-category') ? { name: 'affiliate_category_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #editable="{ item }">
				{{ item.editable ? "Oui" : "Non" }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-affiliate-category')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'affiliate_category_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-affiliate-category')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
