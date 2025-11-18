<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0">
			<button class="btn btn-outline-warning shadow-md me-4" @click="$router.back()">
				<i class="fa-light fa-arrow-left w-4 h-4 mr-2"></i>
				Retour
			</button>
		</div>
		<h2 class="text-lg font-semibold m-auto">Inventaire</h2>
	</div>

	<div class="dashboard-card mt-4 !rounded-none">
		<DataTable
			:headers="headers"
			:items="plateOrders"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucune plaque trouvée"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #serial_number="{ item }">
				<KeyField :value="item.serial_number" />
			</template>
			<template #plate_color="{ item }">
				<div :style="{ backgroundColor: item.plate_color?.color_code }" class="color-box"></div>
			</template>
			<template #plate_shape="{ item }">
				{{ item.plate_shape.name }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import { ref, watch } from "vue";
	import client from "@/assets/js/axios/client.js";
	import { formatURLSearchParams } from "@/helpers/utils.js";
	import dayjs from "dayjs";
	import DataTable from "@/components/DataTable.vue";
	import KeyField from "@/components/KeyField.vue";

	const options = ref({});

	const headers = [
		{ title: "Numéro", key: "serial_number", sortable: true },
		{ title: "Forme", key: "plate_shape" },
		{ title: "Couleur", key: "plate_color" },
		{ title: "Date", key: "created_at", sortable: true },
	];

	const plateOrders = ref([]);
	const meta = ref({});
	const loading = ref(true);

	const fetchDeclarations = async (metadata) => {
		client({
			method: "GET",
			url: "/plates?" + formatURLSearchParams(metadata).toString(),
		})
			.then((response) => response.data)
			.then((response) => {
				plateOrders.value = response.data;

				meta.value = {
					current_page: response.current_page,
					total: response.total,
					per_page: response.per_page,
					from: response.from,
					to: response.to,
					links: response.links,
				};

				loading.value = false;
			});
	};

	watch(
		options,
		(newOptions) => {
			loading.value = true;
			fetchDeclarations(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped>
	.color-box {
		width: 20px;
		height: 20px;
		border-radius: 50%;
	}
</style>
