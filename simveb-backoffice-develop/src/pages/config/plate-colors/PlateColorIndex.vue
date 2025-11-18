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
			notyf.success("La couleur de plaque a bien été supprimée");
			await getItems(options.value);
		});
	});

	const urlPath = "/plate-colors";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Libellé", key: "label", sortable: true },
		{ title: "Code couleur", key: "color_code" },
		{ title: "Couleur du texte", key: "text_color" },
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
			create-title="Ajout d'une couleur de plaque"
			:create-link="can('store-plate-color') ? { name: 'plate_color_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #color_code="{ item }">
				<div v-if="item.color_code" class="is-flex is-align-content-center">
					<div>{{ item.color_code }}</div>
					<div class="ml-4 color-code-preview" :style="'background-color: ' + item.color_code"></div>
				</div>
			</template>
			<template #text_color="{ item }">
				<div v-if="item.text_color" class="is-flex is-align-content-center">
					<div>{{ item.text_color }}</div>
					<div class="ml-4 color-code-preview" :style="'background-color: ' + item.text_color"></div>
				</div>
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-plate-color')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'plate_color_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-plate-color')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style lang="scss">
	.color-code-preview {
		width: 20px;
		height: 20px;
		border: 1px solid black;
		border-radius: 50%;
	}
</style>
