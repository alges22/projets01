<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Ajout d'un type de propriétaire"
			:create-button="can('store-owner-type')"
			empty-text="Liste vide"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #plate_colors="{ item }">
				{{ item.plate_colors ? displayPlateColors(item.plate_colors) : null }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-owner-type')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton
						v-if="can('update-owner-type')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification d\'un type de propriétaire' : 'Ajout d\'un type de propriétaire'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Libellé" horizontal>
						<VControl fullwidth :errors="errors.label || []">
							<VInput v-model="row.label" name="label" placeholder="Libellé" required />
						</VControl>
					</VField>

					<VField label="Couleurs de plaque" horizontal>
						<VControl fullwidth :errors="errors.plate_colors || []">
							<v-select
								v-model="row.plate_colors"
								multiple
								:options="formData.plate_colors"
								label="label"
								:reduce="(item) => item.id"
							></v-select>
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
	import dayjs from "dayjs";
	import { useCrudStore } from "/src/stores/modules/crud";
	import { userHasPermissions } from "/src/utils/permission";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url, row, formLoading, errors, formData } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();
	const modalIsOpen = ref(false);
	const update = ref(false);

	const headers = [
		{ title: "Libellé", key: "label", sortable: true },
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Couleurs de plaque", key: "plate_colors" },
		{ title: "Date de création", key: "created_at", sortable: true },
	];

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le type de propriétaire a bien été supprimé");
			await getItems(options.value);
		});
	});

	onBeforeMount(() => {
		url.value = "/owner-types";
	});

	const getItems = async (metadata) => {
		url.value = "/owner-types";
		await crudStore.fetchRows(metadata);
	};

	const handleUpdate = (item) => {
		row.value = {
			...item,
			plaque_colors: item.plate_colors.map((color) => color.id),
		};
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

	onMounted(() => {
		crudStore.loadCreateData();
	});

	onUnmounted(() => {
		crudStore.reset();
	});

	const displayPlateColors = (colorsObject) => {
		let colors = "";
		colorsObject.forEach((elem) => {
			colors += elem.label + ", ";
		});

		return colors.substring(0, colors.length - 2);
	};
</script>
