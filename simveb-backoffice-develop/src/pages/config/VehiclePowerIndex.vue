<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Ajout d'un type de véhicule"
			:create-button="can('store-vehicule-power')"
			empty-text="Liste vide"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-vehicule-power')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton
						v-if="can('delete-vehicule-power')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			v-if="can('store-vehicule-power') || can('update-vehicule-power')"
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification d\'un motif' : 'Ajout d\'un motif'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Unité" horizontal>
						<VControl fullwidth :errors="errors.unit || []">
							<VInput v-model="row.unit" name="unit" placeholder="Unité" required />
						</VControl>
					</VField>
					<VField label="Minimum" horizontal>
						<VControl fullwidth :errors="errors.min_value || []">
							<VInput
								v-model="row.min_value"
								name="min_value"
								placeholder="Minimum"
								required
								type="number"
								min="1"
							/>
						</VControl>
					</VField>
					<VField label="Maximum" horizontal>
						<VControl fullwidth :errors="errors.max_value || []">
							<VInput
								v-model="row.max_value"
								name="max_value"
								placeholder="Maximum"
								required
								type="number"
								min="1"
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
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url, row, formLoading, errors } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();
	const modalIsOpen = ref(false);
	const update = ref(false);

	onBeforeMount(() => {
		url.value = "/vehicule-powers";
	});

	const getItems = async (metadata) => {
		url.value = "/vehicule-powers";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Unité", key: "unit", sortable: true },
		{ title: "Minimum", key: "min_value", sortable: true },
		{ title: "Maximum", key: "max_value", sortable: true },
	];

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

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("L'élément a bien été supprimé");
			await getItems(options.value);
		});
	});

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
