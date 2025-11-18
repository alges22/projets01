<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Création d'un motif de titre"
			:create-button="can('store-title-reason')"
			empty-text="Aucun motif de titre trouvé"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-title-reason')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>

					<VIconButton
						v-if="can('delete-title-reason')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			v-if="can('store-title-reason') || can('update-title-reason')"
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification d\'un motif' : 'Ajout d\'un motif'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Label du motif" horizontal>
						<VControl :errors="errors.label" :loading="formLoading">
							<VInput v-model="row.label" name="label" required />
						</VControl>
					</VField>
					<VField label="Description" horizontal>
						<VControl :errors="errors.description" :loading="formLoading">
							<VInput v-model="row.description" name="description" required />
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
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const notyf = new Notyf();

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows, meta, loading, url, row, formLoading, errors } = storeToRefs(crudStore);
	const options = ref({});
	const modalIsOpen = ref(false);
	const update = ref(false);

	const headers = [
		{ title: "Label", key: "label" },
		{ title: "Description", key: "description" },
	];

	onBeforeMount(() => {
		url.value = "/title-reasons";
	});

	const getItems = async (metadata) => {
		url.value = "/title-reasons";
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

	onUnmounted(() => {
		crudStore.reset();
	});

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le motif de titre a bien été supprimé");
			await getItems(options.value);
		});
	});
</script>

<style scoped lang="scss"></style>
