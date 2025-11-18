<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Création d'un parc"
			:create-button="can('store-park')"
			empty-text="Aucun parc trouvé"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #affiliate="{ item }">
				{{ item.affiliate?.company_name }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-park')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton
						v-if="can('delete-park')"
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
			:title="update ? 'Modification d\'un motif' : 'Ajout d\'un motif'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField horizontal label="Nom">
						<VControl fullwidth :errors="errors.name" :loading="formLoading">
							<VInput v-model="row.name" name="name" placeholder="Nom" required type="text" />
						</VControl>
					</VField>
					<VField horizontal label="Adresse">
						<VControl fullwidth :errors="errors.address" :loading="formLoading">
							<VInput v-model="row.address" name="address" placeholder="Adresse" required type="text" />
						</VControl>
					</VField>
					<VField horizontal label="Description">
						<VControl fullwidth :errors="errors.description" :loading="formLoading">
							<VInput
								v-model="row.description"
								name="description"
								placeholder="Description"
								required
								type="text"
							/>
						</VControl>
					</VField>
					<VField horizontal label="Longitude">
						<VControl fullwidth :errors="errors.longitude" :loading="formLoading">
							<VInput
								v-model="row.longitude"
								name="longitude"
								placeholder="Longitude"
								required
								type="number"
							/>
						</VControl>
					</VField>
					<VField horizontal label="Latitude">
						<VControl fullwidth :errors="errors.latitude" :loading="formLoading">
							<VInput
								v-model="row.latitude"
								name="latitude"
								placeholder="Latitude"
								required
								type="number"
							/>
						</VControl>
					</VField>
					<VField horizontal label="Affilié">
						<VControl fullwidth :errors="errors.affiliate_id" :loading="formLoading">
							<v-select
								v-model="row.affiliate_id"
								:options="formData.affiliate"
								:reduce="(item) => item.id"
								label="social_reason"
								placeholder="Sélectionnez l'affilié"
							></v-select>
						</VControl>
					</VField>
					<VField horizontal label="Types de véhicule">
						<VControl fullwidth :errors="errors.vehicle_types" :loading="formLoading">
							<v-select
								multiple
								v-model="row.vehicle_types"
								:options="formData.vehicle_types"
								:reduce="(item) => item.id"
								label="label"
								placeholder="Sélectionnez le type de véhicule"
							></v-select>
						</VControl>
					</VField>
					<VField horizontal label="Catégorie de véhicule">
						<VControl fullwidth :errors="errors.vehicle_categories" :loading="formLoading">
							<v-select
								multiple
								v-model="row.vehicle_categories"
								:options="formData.vehicle_categories"
								:reduce="(item) => item.id"
								label="label"
								placeholder="Sélectionnez la catégorie de véhicule"
							></v-select>
						</VControl>
					</VField>
					<VField horizontal label="Communes">
						<VControl fullwidth :errors="errors.towns" :loading="formLoading">
							<v-select
								multiple
								v-model="row.towns"
								:options="formData.towns"
								:reduce="(item) => item.id"
								label="name"
								placeholder="Sélectionnez les communes"
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
	import { onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows, meta, loading, url, row, formLoading, errors, formData } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();
	const modalIsOpen = ref(false);
	const update = ref(false);

	const getItems = async (metadata) => {
		url.value = "/parks";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Adresse", key: "address", sortable: true },
		{ title: "Longitude", key: "longitude", sortable: true },
		{ title: "Latitude", key: "latitude", sortable: true },
		{ title: "Nom de la compagnie", key: "affiliate" },
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
			notyf.success("Le parc a bien été supprimé");
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

	onMounted(async () => {
		await crudStore.loadCreateData();
	});

	onUnmounted(() => {
		crudStore.reset();
	});
</script>
