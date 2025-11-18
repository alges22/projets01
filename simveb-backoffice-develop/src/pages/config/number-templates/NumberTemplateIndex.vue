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

	const urlPath = "/number-templates";

	const notyf = new Notyf();

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [{ title: "Template", key: "template", sortable: true }];

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

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le staff a bien été supprimé");
			await getItems(options.value);
		});
	});
</script>

<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Création d'un modèle de numéro"
			:create-link="can('store-number-template') ? { name: 'number_template_create' } : null"
			empty-text="Aucun modèle trouvé"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-number-template')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'number_template_create',
							params: { id: item.id },
						}"
					/>

					<VIconButton
						v-if="can('delete-number-template')"
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
