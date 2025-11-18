<script setup>
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { onMounted, ref, watch } from "vue";
	import client from "@/assets/js/axios/client.js";
	import dayjs from "dayjs";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import { formatURLSearchParams } from "@/helpers/utils.js";

	const { can } = userHasPermissions();

	const headers = [
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const oppositions = ref([]);
	const meta = ref({});
	const loading = ref(true);

	const options = ref({});

	const fetchOppositions = (options) => {
		client({
			method: "GET",
			url: "/oppositions?" + formatURLSearchParams(options).toString(),
		})
			.then((response) => response.data)
			.then((response) => {
				oppositions.value = response.data;

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

	onMounted(() => {
		fetchOppositions(options);
	});

	watch(
		options,
		(newOptions) => {
			fetchOppositions(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<template>
	<div class="flex mt-8 justify-end">
		<RouterLink v-if="can('store-opposition')" class="btn btn-primary shadow-md mr-2" to="opposition">
			Demande d'opposition
		</RouterLink>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
		<div class="intro-y flex items-center col-span-12">
			<h2 class="mr-auto text-lg font-bold">Demandes de mise en opposition</h2>
		</div>
		<DataTable
			:headers="headers"
			:items="oppositions"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune opposition trouvée"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #owner_name="{ item }">
				{{ item.owner.identity ? item.owner.identity.fullName : item.owner.institution?.name }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY HH:mm") }}
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #actions="{ item }">
				<RouterLink :to="/opposition/ + item.id">Voir détails</RouterLink>
			</template>
		</DataTable>
	</div>
</template>

<style lang="scss" scoped></style>
