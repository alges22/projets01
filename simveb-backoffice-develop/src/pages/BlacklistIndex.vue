<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Ajouter une personne sur la liste noire"
			:create-button="can('store-blacklist-person')"
			empty-text="Liste vide"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-blacklist-person')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton v-if="can('delete-blacklist-person')" class="ml-2 has-text-danger" icon="trash" />
				</div>
			</template>
		</DataTable>

		<VModal
			v-if="can('store-blacklist-person') || can('update-blacklist-person')"
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification' : 'Ajout'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="IFU" horizontal>
						<VControl fullwidth :errors="errors.ifu || []">
							<VInput v-model="row.ifu" type="text" placeholder="IFU" name="ifu" />
						</VControl>
					</VField>
					<VField label="NPI" horizontal>
						<VControl fullwidth :errors="errors.npi || []">
							<VInput v-model="row.npi" type="text" placeholder="NPI" name="npi" />
						</VControl>
					</VField>
					<VField label="Numéro de plaque" horizontal>
						<VControl fullwidth :errors="errors.plate_number || []">
							<VInput
								v-model="row.plate_number"
								type="text"
								placeholder="Numéro de plaque"
								name="plate_number"
							/>
						</VControl>
					</VField>
					<VField label="Numéro de chassis / VIN" horizontal>
						<VControl fullwidth :errors="errors.vin || []">
							<VInput v-model="row.vin" type="text" placeholder="" name="chassis_number" />
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

	const notyf = new Notyf();

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows, meta, loading, url, row, formLoading, errors } = storeToRefs(crudStore);
	const options = ref({});
	const modalIsOpen = ref(false);
	const update = ref(false);

	onBeforeMount(() => {
		url.value = "/blacklist-persons";
	});

	const getItems = async (metadata) => {
		url.value = "/blacklist-persons";
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
		await getItems(options.value);
		row.value = {};
	};

	const headers = [
		{ title: "IFU", key: "ifu" },
		{ title: "NPI", key: "npi" },
		{ title: "Numéro de plaque", key: "plate_number" },
		{ title: "Numéro de chassis / VIN", key: "vin" },
	];

	onUnmounted(() => {
		crudStore.reset();
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
