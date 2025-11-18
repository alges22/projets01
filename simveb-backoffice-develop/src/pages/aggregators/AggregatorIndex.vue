<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import dayjs from "dayjs";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const { can } = userHasPermissions();
	const notyf = new Notyf();

	const crudStore = useCrudStore();
	const { rows, row, meta, loading, url, formLoading } = storeToRefs(crudStore);
	const options = ref({});

	const { handleDelete } = useDeleteConfirmation((item) => {
		crudStore.deleteRow(item.id).then(() => {
			notyf.success("Le type de document a bien été supprimé");
		});
	}, "Êtes vous sur de vouloir supprimer le type de document ?");

	const urlPath = "/document-types";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Code", key: "code", sortable: true },
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
			create-title="Ajouter un aggrégateur de paiement"
			:create-link="can('store-document-type') ? { name: 'aggregators_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }"> {{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-document-type')"
						:to="{
							name: 'aggregator_edit',
							params: { id: item.id },
						}"
						class="has-text-primary"
						icon="edit"
					/>
					<VIconButton
						v-if="can('delete-document-type')"
						:loading="formLoading"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
