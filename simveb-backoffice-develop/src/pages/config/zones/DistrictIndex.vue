<template>
	<div class="page-content-inner">
		<ImportZone />

		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune commune trouvé"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #commune="{ item }">
				{{ item.town?.name }}
			</template>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});

	onBeforeMount(() => {
		url.value = "/districts";
	});

	const getItems = async (metadata) => {
		url.value = "/districts";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Code", key: "code", sortable: true },
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Commune", key: "commune", sortable: true },
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

<style scoped lang="scss"></style>
