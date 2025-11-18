<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});

	onBeforeMount(() => {
		url.value = "/borders";
	});

	const getItems = async (metadata) => {
		url.value = "/borders";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Longitude", key: "longitude", sortable: true },
		{ title: "Latitude", key: "latitude", sortable: true },
		{ title: "Pays frontalier", key: "country" },
		{ title: "Ville", key: "town" },
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
			create-title="Création d'une frontière"
			:create-link="can('store-border') ? { name: 'frontieres_new' } : null"
			empty-text="Aucun frontière trouvé"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #country="{ item }">
				{{ item.country?.name }}
			</template>
			<template #town="{ item }">
				{{ item.town?.name }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-border')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'frontieres_edit',
							params: { id: item.id },
						}"
					/>
					<VIconButton v-if="can('delete-border')" class="ml-2 has-text-danger" icon="trash" />
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style scoped lang="scss"></style>
