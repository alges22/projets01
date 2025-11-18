<script setup>
	import CallToActionCard from "@/components/CallToActionCard.vue";
	import DataTable from "@/components/DataTable.vue";
	import { onMounted, ref, watch } from "vue";
	import client from "@/assets/js/axios/client.js";
	import StatusComponent from "@/components/StatusComponent.vue";
	import dayjs from "dayjs";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import { useRouter } from "vue-router";
	import { formatURLSearchParams } from "@/helpers/utils.js";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "institution", title: "Banque/Concessionnaire", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "vin", title: "Vin", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const pledges = ref([]);
	const meta = ref({});
	const loading = ref(true);
	const { can, hasOnePermissions } = userHasPermissions();

	const options = ref({});

	const router = useRouter();

	const goTo = (link) => {
		router.push({ name: link });
	};

	const fetchPledges = (options) => {
		client({
			method: "GET",
			url: "/pledge?" + formatURLSearchParams(options).toString(),
		})
			.then((response) => response.data)
			.then((response) => {
				pledges.value = response.data;

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
		fetchPledges(options);
	});

	watch(
		options,
		(newOptions) => {
			fetchPledges(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<template>
	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12">
			<div class="grid grid-cols-12 gap-6">
				<DashboardStats />
			</div>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-10">
		<div
			v-if="hasOnePermissions(['store-pledge-by-distributor', 'store-pledge-by-bank'])"
			class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md"
		>
			<CallToActionCard
				button-text="Émettre gage"
				image-alt="Cliquez pour émettre un gage"
				subtitle="Petite description"
				title="Émission de gage"
				@click="goTo('pledge-issue')"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
					<img alt="Cliquez pour émettre un gage" class="" src="@/assets/images/parc/apps.png" />
				</div>
			</CallToActionCard>
		</div>

		<div v-if="can('lift-pledge')" class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Lever gage"
				image-alt="Cliquez pour lever un gage"
				subtitle="Petite description"
				title="Levée de gage"
				@click="goTo('pledge-can-lift')"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
					<img alt="Cliquez pour lever un gage" class="" src="@/assets/images/parc/apps.png" />
				</div>
			</CallToActionCard>
		</div>
	</div>

	<h4 class="mt-16 font-bold text-xl">Demandes de mise en gage</h4>
	<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
		<DataTable
			:headers="headers"
			:items="pledges"
			:loading="loading"
			:meta="meta"
			empty-text="Aucun gage trouvé"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #vin="{ item }">
				{{ item.vehicle.vin }}
			</template>
			<template #institution="{ item }">
				{{ item.institution_emitted.acronym }} - {{ item.institution_emitted.name }}
			</template>
			<template #owner_name="{ item }">
				{{
					item.vehicle_owner.identity
						? item.vehicle_owner.identity.fullName
						: item.vehicle_owner.institution?.name
				}}
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
			</template>
			<template #actions="{ item }">
				<RouterLink v-if="can('show-pledge')" :to="/pledge/ + item.id">Voir détails</RouterLink>
			</template>
		</DataTable>
	</div>
</template>

<style scoped lang="scss"></style>
