<script setup lang="ts">
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { useHead } from "@vueuse/head";
	import { ref } from "vue";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import client from "/@src/composable/axiosClient";
	import { meta } from "eslint-plugin-prettier-vue/lib/rules/prettier/meta";

	const viewWrapper = useViewWrapper();
	viewWrapper.setPageTitle("Gestion des prix des services");

	useHead({
		title: "Gestion des prix des services - Simveb",
	});

	const rows = ref([]);
	const meta = ref({});

	onMounted(() => {
		client({
			method: "GET",
			url: `prices`,
		}).then((response) => {
			rows.value = response.data;

			meta.value = {
				itemsPerPage: response.per_page,
				totalItems: response.total,
				currentPage: response.current_page,
			};
		});
	});
</script>

<template>
	<div class="page-content-inner">
		<VCard elevated class="mt-5">
			<VGrid grid-template-columns="repeat(4, 1fr)" column-gap="10">
				<VCard> 1111 </VCard>
				<VCard />
				<VCard />
				<VCard />
			</VGrid>

			<VFlexPagination
				:item-per-page="meta.itemsPerPage"
				:total-items="meta.totalItems"
				:current-page="meta.currentPage"
				:max-links-displayed="5"
			/>
		</VCard>
	</div>
</template>

<style scoped lang="scss"></style>
