<script setup>
	import DataTable from "@/components/DataTable.vue";
	import { ref } from "vue";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "bank", title: "Banque", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "vin", title: "Vin", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const data = ref();
</script>

<template>
	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12">
			<div class="grid grid-cols-12 gap-6">
				<DashboardStats />
			</div>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-4 dashboard-card">
		<div class="intro-y mt-8 flex items-center col-span-12">
			<h2 class="mr-auto text-lg font-bold">Activité Récentes</h2>
		</div>
		<DataTable
			:headers="headers"
			:items="data"
			create-title="Créer un produit d'épargne"
			empty-text="Aucun produit d'épargne trouvé"
			search
		>
			<template #statut>
				<span class="bg-green-50 p-2 rounded-full">
					<span class="size-2 inline-block bg-green-600 rounded-full me-2 dark:bg-green-500"></span>
					<span class="font-bold text-green-600">Approuver</span>
				</span>
			</template>
			<template #actions>
				<a href="#">Voir détails</a>
			</template>
		</DataTable>
	</div>
</template>

<style lang="scss" scoped></style>
