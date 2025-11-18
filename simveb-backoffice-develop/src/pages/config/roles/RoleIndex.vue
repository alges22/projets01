<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
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
			notyf.success("Le rôle a bien été supprimé");
			await getItems(options.value);
		});
	});

	const urlPath = "/roles";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Libellé", key: "label" },
		{ title: "Modifiable", key: "editable" },
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
			create-title="Ajout d'un rôle"
			:create-link="can('store-role') ? { name: 'role_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #editable="{ item }">
				{{ item.editable ? "Oui" : "Non" }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-role')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'role_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-role')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
