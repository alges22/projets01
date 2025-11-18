<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Ajout d'un type de document"
			:create-button="can('store-document-type')"
			empty-text="Liste vide"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }"> {{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-document-type')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
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

		<VModal
			v-if="can('store-document-type') || can('update-document-type')"
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification d\'un type de document' : 'Ajout d\'un type de document'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Code" horizontal>
						<VControl fullwidth :errors="errors.code || []">
							<VInput v-model="row.code" type="text" placeholder="Code" name="label" required />
						</VControl>
					</VField>
					<VField label="Description" horizontal>
						<VControl fullwidth :errors="errors.description || []">
							<VTextarea
								v-model="row.description"
								type="text"
								placeholder="Description"
								name="description"
							/>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton :loading="formLoading" :disabled="formLoading" color="primary" raised @click="handleSubmit">
					Enregistrer
				</VButton>
			</template>
		</VModal>
	</div>
</template>

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
	const { rows, row, meta, loading, url, formLoading, errors } = storeToRefs(crudStore);
	const options = ref({});
	const modalIsOpen = ref(false);
	const update = ref(false);

	const headers = [
		{ title: "Code", key: "code", sortable: true },
		{ title: "Description", key: "description" },
		{ title: "Date de création", key: "created_at", sortable: true },
	];

	onBeforeMount(() => {
		url.value = "/document-types";
	});

	const getItems = async (metadata) => {
		url.value = "/document-types";
		await crudStore.fetchRows(metadata);
	};

	const handleUpdate = (item) => {
		row.value = item;
		update.value = true;
		modalIsOpen.value = true;
	};

	const handleSubmit = async () => {
		if (update.value) {
			await crudStore.updateRow(row.value.id, row.value).then(() => {
				modalIsOpen.value = false;
				notyf.success("Modification effectuée avec succès!");
			});
		} else {
			await crudStore.createRow(row.value).then(() => {
				modalIsOpen.value = false;
				notyf.success("Enregistrement effectué avec succès!");
			});
		}
		update.value = false;
		await getItems(options.value);
		row.value = {};
	};

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);

	const { handleDelete } = useDeleteConfirmation((item) => {
		crudStore.deleteRow(item.id).then(() => {
			notyf.success("Le type de document a bien été supprimé");
		});
	}, "Êtes vous sur de vouloir supprimer le type de document ?");

	onUnmounted(() => {
		crudStore.reset();
	});
</script>
