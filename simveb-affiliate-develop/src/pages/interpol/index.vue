<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Tableau de bord / Interpol</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="grid grid-cols-12 gap-6">
			<div class="col-span-12">
				<div class="grid grid-cols-12 gap-6">
					<div class="col-span-12 mt-8 dashboard-card p-8">
						<div class="intro-y flex items-center h-10">
							<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
						</div>
						<div class="grid grid-cols-12 gap-6 mt-5">
							<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
								<MetaCard
									background-color="bg-green-100"
									icon="check"
									icon-color="success"
									title="Validations"
								>
									643
								</MetaCard>
							</div>
							<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
								<MetaCard
									background-color="bg-yellow-100"
									icon="hourglass-end"
									icon-color="warning"
									title="En attente"
								>
									230
								</MetaCard>
							</div>
							<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
								<MetaCard background-color="bg-red-100" icon="hand" icon-color="danger" title="Rejets">
									102
								</MetaCard>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
			<div class="intro-y mt-8 flex items-center col-span-12">
				<h2 class="mr-auto text-lg font-bold">Activité Récentes</h2>
			</div>
			<DataTable
				:headers="headers"
				:items="data"
				empty-text="Aucun produit d'épargne trouvé"
				header-class="uppercase text-start"
				search
			>
				<template #statut="{ item }">
					<StatusComponent :status="item.statut" />
				</template>
				<template #actions>
					<a href="#">Voir détails</a>
				</template>
			</DataTable>
		</div>
	</div>
</template>

<script setup>
	import MetaCard from "@/components/MetaCard.vue";
	import DataTable from "@/components/DataTable.vue";
	import { onBeforeMount, ref } from "vue";
	import { storeToRefs } from "pinia";
	import { usePageTitleStore } from "@/stores/page-title.js";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import NotificationBox from "@/components/NotificationBox.vue";
	import StatusComponent from "@/components/StatusComponent.vue";

	const { pageTitle } = storeToRefs(usePageTitleStore());

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "bank", title: "Banque", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "vin", title: "Vin", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const data = ref([
		{
			key: 1,
			reference: "REF-0001",
			bank: "BANK-0001",
			owner_name: "John Doe",
			vin: "VIN-0001",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 2,
			reference: "REF-0002",
			bank: "BANK-0002",
			owner_name: "Jane Doe",
			vin: "VIN-0002",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 3,
			reference: "REF-0003",
			bank: "BANK-0003",
			owner_name: "John Doe",
			vin: "VIN-0003",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
		{
			key: 4,
			reference: "REF-0004",
			bank: "BANK-0004",
			owner_name: "Jane Doe",
			vin: "VIN-0004",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 5,
			reference: "REF-0005",
			bank: "BANK-0005",
			owner_name: "John Doe",
			vin: "VIN-0005",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 6,
			reference: "REF-0006",
			bank: "BANK-0006",
			owner_name: "Jane Doe",
			vin: "VIN-0006",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
		{
			key: 7,
			reference: "REF-0007",
			bank: "BANK-0007",
			owner_name: "John Doe",
			vin: "VIN-0007",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 8,
			reference: "REF-0008",
			bank: "BANK-0008",
			owner_name: "Jane Doe",
			vin: "VIN-0008",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 9,
			reference: "REF-0009",
			bank: "BANK-0009",
			owner_name: "John Doe",
			vin: "VIN-0009",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
		{
			key: 10,
			reference: "REF-0010",
			bank: "BANK-0010",
			owner_name: "Jane Doe",
			vin: "VIN-0010",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
	]);

	onBeforeMount(() => {
		pageTitle.value = "Tableau de bord / Concessionnaire - Parc";
	});
</script>

<style scoped></style>
