<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Créer une institution"
			:create-link="can('store-institution') ? { name: 'institutions_create' } : null"
			empty-text="Aucune institution trouvée"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #logo="{ item }">
				<img v-if="item.logo" :src="item.logo" :alt="item.name" width="50" height="50" />
			</template>
			<template #name="{ item }">
				{{ `(${item.acronym}) ${item.name}` }}
			</template>
			<template #commune="{ item }">
				<img v-if="item.logo" :src="item.logo" :alt="item.name" />
				{{ item.district?.name }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-institution')"
						class="has-text-primary"
						icon="edit"
						@click="
							$router.push({
								name: 'institutions_create',
								params: { id: item.id },
							})
						"
					/>
					<VIconButton
						v-if="can('delete-institution')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const notyf = new Notyf();

	onBeforeMount(() => {
		url.value = "/institutions";
	});

	const getItems = async (metadata) => {
		url.value = "/institutions";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Logo", key: "logo" },
		{ title: "Nom", key: "name" },
		{ title: "Email", key: "email" },
		{ title: "IFU", key: "ifu" },
		{ title: "Téléphone", key: "telephone" },
		{ title: "Adresse", key: "address" },
	];

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le service a bien été supprimé");
			await getItems(options.value);
		});
	});

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true },
	);

	onUnmounted(() => {
		crudStore.reset();
	});
</script>

<style scoped lang="scss"></style>
