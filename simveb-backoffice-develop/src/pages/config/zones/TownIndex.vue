<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="towns"
			:loading="loading"
			:meta="meta"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";

	const crudStore = useCrudStore();
	const { rows: towns, loading, url, meta } = storeToRefs(crudStore);
	const options = ref({});

	const headers = [
		{ title: "Code", key: "code" },
		{ title: "Nom", key: "name", sortable: false },
	];

	onBeforeMount(() => {
		url.value = "/towns";
	});

	const getTowns = async (metadata: Array) => {
		url.value = "/towns";
		await crudStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getTowns(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		crudStore.reset();
	});
</script>

<style scoped lang="scss"></style>
