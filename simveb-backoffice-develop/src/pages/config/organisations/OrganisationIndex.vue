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
			notyf.success("L'organisation a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/organizations";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Description", key: "description" },
		{ title: "Parent", key: "parent_id" },
		{ title: "Responsable", key: "responsible" },
		{ title: "Date de création", key: "created_at" },
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
			create-title="Ajout d'une organisation"
			:create-link="can('browse-organization') ? { name: 'organization_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #parent_id="{ item }">
				{{ item.parent?.name }}
			</template>
			<template #responsible="{ item }">
				{{ item.responsible?.identity?.fullName }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY à HH:mm") }}
			</template>
			<template #is_interpol="{ item }">
				<VIconBox v-if="item.is_interpol" size="small" color="success" rounded>
					<i class="fa fa-check"></i>
				</VIconBox>
				<VIconBox v-else size="small" color="danger" rounded>
					<i class="iconify" data-icon="x" aria-hidden="true"></i>
				</VIconBox>
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-organization')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'organization_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-organization')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
