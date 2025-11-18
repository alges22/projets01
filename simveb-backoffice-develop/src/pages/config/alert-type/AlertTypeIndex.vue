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

	const urlPath = "/alert-types";

	const notyf = new Notyf();

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Image", key: "image" },
		{ title: "Nom", key: "name" },
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

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le type d'alerte a bien été supprimé");
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
			create-title="Création d'un type d'alerte"
			:create-link="can('store-alert-type') ? { name: 'alert_types_create' } : null"
			empty-text="Aucun type d'alerte trouvé"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #image="{ item }">
				<img :alt="item.description" class="half-image" :src="item.image" />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-alert-type')"
						class="has-text-primary"
						icon="edit"
						@click="
							$router.push({
								name: 'alert_types_create',
								params: { id: item.id },
							})
						"
					/>

					<VIconButton
						v-if="can('delete-alert-type')"
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
	.half-image {
		width: 50px;
		height: 50px;
	}
</style>
