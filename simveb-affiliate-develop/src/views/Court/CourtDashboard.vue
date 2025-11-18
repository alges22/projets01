<script setup>
	import DataTable from "@/components/DataTable.vue";
	import { ref } from "vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import dayjs from "dayjs";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const headers = {
		gage: [
			{ key: "reference", title: "Réference", sortable: false },
			{ key: "created_at", title: "Date et heure", sortable: false },
			{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
			{ key: "immatriculation", title: "Immatriculation", sortable: false },
			{ key: "vin", title: "VIN (Numéro de chassis)", sortable: false },
			{ key: "institution", title: "Institution financière", sortable: false },
		],
		opposition: [
			{ key: "reference", title: "Réference", sortable: false },
			{ key: "created_at", title: "Date et heure", sortable: false },
			{ key: "demandeur", title: "Demandeur", sortable: false },
			{ key: "immatriculation", title: "Immatriculation", sortable: false },
			{ key: "vin", title: "VIN (Numéro de chassis)", sortable: false },
		],
	};

	const oppositions = ref([]);
	const gages = ref([]);
	const loading = ref(false);
</script>

<template>
	<DashboardStats />

	<div class="mt-5 dashboard-card shadow-md">
		<div class="intro-y flex items-center col-span-12 mb-8">
			<h2 class="ml-4 text-lg font-bold text-blue-900">Récentes demandes de mise en gage enregistrées</h2>
		</div>

		<DataTable
			:headers="headers.gage"
			:items="gages"
			:loading="loading"
			empty-text="Aucune demande de mise en gage enregistrée"
		>
			<template #owner_name="{ item }">
				{{ item.owner.identity.fullName }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY HH:mm") }}
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #actions="{ item }">
				<RouterLink :to="/pledge/ + item.id">Voir détails</RouterLink>
			</template>
		</DataTable>

		<div class="w-full py-2 flex justify-center">
			<RouterLink class="font-bold text-xl text-blue-600" to="/court-pledges">
				Afficher toutes les demandes <i class="fa fa-arrow-right"></i
			></RouterLink>
		</div>
	</div>

	<div class="mt-5 dashboard-card shadow-md">
		<div class="intro-y flex items-center col-span-12 mb-8">
			<h2 class="ml-4 text-lg font-bold text-blue-900">Récentes demandes de mise en opposition</h2>
		</div>

		<DataTable
			:headers="headers.opposition"
			:items="oppositions"
			:loading="loading"
			empty-text="Aucune demande de mise en opposition enregistrée"
		>
			<template #owner_name="{ item }">
				{{ item.owner.identity.fullName }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY HH:mm") }}
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #actions="{ item }">
				<RouterLink :to="/pledge/ + item.id">Voir détails</RouterLink>
			</template>
		</DataTable>

		<div class="w-full py-2 flex justify-center">
			<RouterLink class="font-bold text-xl text-blue-600" to="/oppositions">
				Afficher toutes les demandes <i class="fa fa-arrow-right"></i>
			</RouterLink>
		</div>
	</div>
</template>

<style lang="scss" scoped></style>
