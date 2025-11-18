<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Création d'une catégorie de véhicule"
			:create-button="can('store-vehicle-category')"
			empty-text="Aucune catégorie trouvée"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #date="{ item }"> {{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-vehicle-category')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton
						v-if="can('delete-vehicle-category')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			v-if="can('store-vehicle-category') || can('update-vehicle-category')"
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification d\'un motif' : 'Ajout d\'un motif'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Libellé" horizontal>
						<VControl fullwidth :errors="errors.label || []">
							<VInput v-model="row.label" type="text" placeholder="Libellé" name="label" required />
						</VControl>
					</VField>
					<VField label="Nombre de plaque" horizontal>
						<VControl fullwidth :errors="errors.nb_plate || []">
							<VInput
								v-model="row.nb_plate"
								type="number"
								placeholder="Nombre de plaque"
								min="1"
								required
								name="nb_plate"
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
	import { onUnmounted, ref, watch } from "vue";
	import dayjs from "dayjs";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, row, meta, loading, url, formLoading, errors } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();
	const modalIsOpen = ref(false);
	const update = ref(false);

	const headers = [
		{ title: "Libellé", key: "label", sortable: true },
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Nombre de plaque", key: "nb_plate", sortable: true },
		{ title: "Date", key: "date", sortable: true },
	];

	const getItems = async (metadata) => {
		url.value = "/vehicle-categories";
		await crudStore.fetchRows(metadata);
	};

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("La catégorie de véhicule a bien été supprimé");
			await getItems(options.value);
		});
	});

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
</script>
