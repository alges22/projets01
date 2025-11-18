<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import dayjs from "dayjs";
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
			notyf.success("Le type d'immatriculation a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/immatriculation-types";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "libellé", key: "label" },
		{ title: "Code", key: "code" },
		{ title: "Description", key: "description" },
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
			create-title="Ajout d'un type d'immatriculation"
			:create-link="can('store-immatriculation-type') ? { name: 'immatriculation_types_creation' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-immatriculation-type')"
						v-tooltip="'Modifier'"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'immatriculation_types_creation',
							params: { id: item.id },
						}"
					/>

					<VIconButton
						v-if="can('show-immatriculation-type')"
						v-tooltip="'Détails'"
						class="ml-2 has-text-primary"
						icon="eye"
						:to="{
							name: 'immatriculation_type_show',
							params: { id: item.id },
						}"
					/>

					<VIconButton
						v-if="can('delete-immatriculation-type')"
						class="ml-2 has-text-danger"
						v-tooltip="'Supprimer'"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
