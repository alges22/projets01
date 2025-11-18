<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import dayjs from "dayjs";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le staff a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/staff";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Nom Complet", key: "fullName" },
		{ title: "Poste", key: "position_id" },
		{ title: "email", key: "email" },
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
			create-title="Ajout d'un membre"
			:create-link="can('store-staff') ? { name: 'staff_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #fullName="{ item }">
				{{ item.identity?.fullName }}
			</template>
			<template #position_id="{ item }">
				{{ item.position?.name }}
			</template>
			<template #email="{ item }">
				{{ item.identity?.email }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-staff')"
						v-tooltip="'Modifier'"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'staff_create',
							params: { id: item.id },
						}"
					/>

					<VIconButton
						v-if="can('update-staff')"
						v-tooltip="'Détails'"
						class="ml-2 has-text-primary"
						icon="eye"
						:to="{
							name: 'staff_show',
							params: { id: item.id },
						}"
					/>

					<VIconButton
						v-if="can('delete-staff')"
						v-tooltip="'Supprimer'"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
