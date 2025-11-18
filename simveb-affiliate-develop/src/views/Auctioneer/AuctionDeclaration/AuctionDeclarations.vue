<script setup>
	import { ref, watch } from "vue";
	import DataTable from "@/components/DataTable.vue";
	import client from "@/assets/js/axios/client.js";
	import { formatURLSearchParams } from "@/helpers/utils.js";
	import dayjs from "dayjs";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const options = ref({});

	const headers = [
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: true },
	];

	const auctionDeclarations = ref([]);
	const meta = ref({});
	const loading = ref(true);

	const { can } = userHasPermissions();

	const fetchDeclarations = async (metadata) => {
		client({
			method: "GET",
			url: "/auction-sale-declarations?" + formatURLSearchParams(metadata).toString(),
		})
			.then((response) => response.data)
			.then((response) => {
				auctionDeclarations.value = response.data;

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

<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4 justify-between">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<router-link
				v-if="can('store-auction-sale-declaration')"
				class="btn btn-primary shadow-md mr-2"
				:to="{ name: 'auction-declarations-create' }"
			>
				<i class="fa-light fa-circle-plus w-4 h-4 mr-2"></i>
				Nouvelle vente
			</router-link>
		</div>
	</div>

	<div class="dashboard-card mt-4 !rounded-none">
		<DataTable
			v-if="can('browse-auction-sale-declaration')"
			:headers="headers"
			:items="auctionDeclarations"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucune déclaration de vente trouvée"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #reference="{ item }">
				<span class="font-medium whitespace-nowrap">
					{{ item.reference }}
				</span>
				<div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
					{{ item.institution?.name }}
				</div>
			</template>
			<template #created_at="{ item }">
				<span class="font-medium whitespace-nowrap">
					{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
				</span>
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-auction-sale-declaration')"
					:to="{ name: 'auction-declarations-show', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4"></i>
					Voir la déclaration
				</router-link>
			</template>
		</DataTable>
	</div>
</template>
