<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="zones"
			:loading="loading"
			:meta="meta"
			:create-button="can('store-zone')"
			create-title="Ajouter une zone"
			empty-text="Liste vide"
			search
			@create="handleCreate"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-zone')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton
						v-if="can('delete-zone')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			is="form"
			v-if="can('store-zone') || can('store-zone')"
			:open="modalIsOpen"
			:title="update ? 'Modification d\'une zone' : 'Ajout d\'une zone'"
			actions="right"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField required horizontal label="Code de la zone">
						<VControl :loading="formLoading" :errors="errors.code || []">
							<VInput v-model="zone.code" name="code" placeholder="XX" required />
						</VControl>
					</VField>

					<VField required horizontal label="Nom de la zone">
						<VControl :errors="errors.name || []" :loading="formLoading" fullwidth>
							<VInput v-model="zone.name" name="name" required />
						</VControl>
					</VField>

					<VField required label="Commune" horizontal>
						<VControl fullwidth>
							<v-select
								v-model="zone.towns"
								multiple
								name="towns"
								:options="formData.towns"
								label="name"
								:reduce="(item) => item.id"
							></v-select>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton
					type="submit"
					:disabled="formLoading"
					:loading="formLoading"
					color="primary"
					raised
					@click="handleSubmit"
				>
					Enregistrer
				</VButton>
			</template>
		</VModal>
	</div>
</template>

<script lang="ts" setup>
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { useNotyf } from "/@src/composable/useNotyf";
	import dayjs from "dayjs";
	import { userHasPermissions } from "/@src/utils/permission";

	const notyf = useNotyf();

	const crudStore = useCrudStore();
	const { rows: zones, row: zone, meta, loading, url, formLoading, errors, formData } = storeToRefs(crudStore);
	const options = ref({});
	const modalIsOpen = ref(false);
	const update = ref(false);
	const { can } = userHasPermissions();

	const headers = [
		{ title: "Code", key: "code" },
		{ title: "Nom", key: "name", sortable: false },
		{ title: "Date de création", key: "created_at", sortable: false },
	];

	onBeforeMount(() => {
		url.value = "/zones";
	});

	const getZones = async (metadata: Array) => {
		url.value = "/zones";
		await crudStore.fetchRows(metadata);
	};

	const handleCreate = () => {
		update.value = false;
		modalIsOpen.value = true;
		zone.value = {};
	};

	const handleUpdate = (item) => {
		zone.value = {
			id: item.id,
			code: item.code,
			name: item.name,
			towns: item.towns.map((town) => {
				return town.id;
			}),
		};

		update.value = true;
		modalIsOpen.value = true;
	};

	const handleSubmit = async () => {
		if (update.value) {
			await crudStore.updateRow(zone.value.id, zone.value).then(() => {
				modalIsOpen.value = false;
				notyf.success("La zone a été modifiée avec succès");
			});
		} else {
			await crudStore.createRow(zone.value).then(() => {
				modalIsOpen.value = false;
				notyf.success("La zone a été ajoutée avec succès");
			});
		}
		await getZones(options.value);
		zone.value = {};
	};

	const handleDelete = async (item) => {
		Swal.fire({
			title: "Êtes-vous sûr?",
			text: "Vous ne pourrez pas revenir en arrière!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, supprimer!",
			cancelButtonText: "Non, annuler!",
			reverseButtons: true,
		}).then(async (result) => {
			if (result.isConfirmed) {
				await crudStore.deleteRow(item.id).then(async () => {
					notyf.success("message.suppression.success");
					await getZones(options.value);
				});
			}
		});
	};

	watch(
		options,
		(newOptions) => {
			getZones(newOptions);
		},
		{ deep: true, immediate: true },
	);

	onMounted(() => {
		crudStore.loadCreateData();
	});

	onUnmounted(() => {
		crudStore.reset();
	});
</script>

<style scoped lang="scss"></style>
