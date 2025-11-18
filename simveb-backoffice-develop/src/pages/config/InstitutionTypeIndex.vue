<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Création d'un type d'institution"
			:create-button="can('store-institution-type')"
			empty-text="Aucun type d'institution trouvé"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-institution-type')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>

					<VIconButton
						v-if="can('delete-institution-type')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			v-if="can('store-institution-type') || can('update-institution-type')"
			is="form"
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification d\'un type d\'institution' : 'Ajout d\'un type d\'institution'"
			@submit.prevent="handleSubmit"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Nom du type d'institution" horizontal>
						<VControl :errors="errors.name" :loading="formLoading">
							<VInput v-model="row.name" name="name" required />
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
				<VButton type="submit" :loading="formLoading" :disabled="formLoading" color="primary" raised>
					Enregistrer
				</VButton>
			</template>
		</VModal>
	</div>
</template>

<script setup lang="ts">
	import { userHasPermissions } from "/src/utils/permission";
	import { useCrudStore } from "/src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onUnmounted, ref, watch } from "vue";
	import { useDeleteConfirmation } from "/src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const notyf = new Notyf();
	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, row, meta, loading, url, formLoading, errors } = storeToRefs(crudStore);
	const options = ref({});
	const modalIsOpen = ref(false);
	const update = ref(false);

	const headers = [
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Description", key: "description", sortable: true },
	];

	const getItems = async (metadata) => {
		url.value = "/institution-types";
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
			notyf.success("Le type d'institution a bien été supprimé");
			await getItems(options.value);
		});
	});
</script>

<style scoped lang="scss"></style>
