<template>
	<SideBar>
		<li>
			<MenuLink :active="$route.path === '/interpol'" icon="gauge-max" label="Tableau de bord" to="/interpol" />
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="suitcase"
				label="Dossiers"
				to="interpol/application"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="book"
				label="Certificats de gages"
				to="bank/pledge"
			/>
		</li>
	</SideBar>

	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Tableau de bord/Banque</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="intro-y flex flex-col sm:flex-row items-center mt-8 bg-white p-8">
			<span class="font-bold">Souhaitez-vous émettre ou lever un gage ?</span>
			<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
				<button class="btn text-red-600 border border-red-600 shadow-md mr-2">Lever un gage</button>
				<router-link to="/bank/pledge/MakePledge" class="btn btn-primary shadow-md mr-2"
					>Emettre un gage</router-link
				>
			</div>
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
									title="Validations"
									icon="check"
									icon-color="success"
									background-color="bg-green-100"
								>
									1.000
								</MetaCard>
							</div>
							<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
								<MetaCard
									title="En attente"
									icon="hourglass-end"
									icon-color="warning"
									background-color="bg-yellow-100"
								>
									100
								</MetaCard>
							</div>
							<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
								<MetaCard title="Rejets" icon="hand" icon-color="danger" background-color="bg-red-100">
									10
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
	</div>
	<router-view />
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import NotificationBox from "@/components/NotificationBox.vue";

	import MetaCard from "@/components/MetaCard.vue";
	import DataTable from "@/components/DataTable.vue";
	import { ref } from "vue";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "bank", title: "Banque", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "vin", title: "Vin", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	// copilot, give me 10 fake data

	const data = ref([
		{
			key: 1,
			reference: "REF-0001",
			bank: "BANK-0001",
			owner_name: "John Doe",
			vin: "VIN-0001",
			created_at: "2021-10-10 10:00:00",
			statut: "Validé",
		},
		{
			key: 2,
			reference: "REF-0002",
			bank: "BANK-0002",
			owner_name: "Jane Doe",
			vin: "VIN-0002",
			created_at: "2021-10-10 10:00:00",
			statut: "En attente",
		},
		{
			key: 3,
			reference: "REF-0003",
			bank: "BANK-0003",
			owner_name: "John Doe",
			vin: "VIN-0003",
			created_at: "2021-10-10 10:00:00",
			statut: "Rejeté",
		},
		{
			key: 4,
			reference: "REF-0004",
			bank: "BANK-0004",
			owner_name: "Jane Doe",
			vin: "VIN-0004",
			created_at: "2021-10-10 10:00:00",
			statut: "Validé",
		},
		{
			key: 5,
			reference: "REF-0005",
			bank: "BANK-0005",
			owner_name: "John Doe",
			vin: "VIN-0005",
			created_at: "2021-10-10 10:00:00",
			statut: "En attente",
		},
		{
			key: 6,
			reference: "REF-0006",
			bank: "BANK-0006",
			owner_name: "Jane Doe",
			vin: "VIN-0006",
			created_at: "2021-10-10 10:00:00",
			statut: "Rejeté",
		},
		{
			key: 7,
			reference: "REF-0007",
			bank: "BANK-0007",
			owner_name: "John Doe",
			vin: "VIN-0007",
			created_at: "2021-10-10 10:00:00",
			statut: "Validé",
		},
		{
			key: 8,
			reference: "REF-0008",
			bank: "BANK-0008",
			owner_name: "Jane Doe",
			vin: "VIN-0008",
			created_at: "2021-10-10 10:00:00",
			statut: "En attente",
		},
		{
			key: 9,
			reference: "REF-0009",
			bank: "BANK-0009",
			owner_name: "John Doe",
			vin: "VIN-0009",
			created_at: "2021-10-10 10:00:00",
			statut: "Rejeté",
		},
		{
			key: 10,
			reference: "REF-0010",
			bank: "BANK-0010",
			owner_name: "Jane Doe",
			vin: "VIN-0010",
			created_at: "2021-10-10 10:00:00",
			statut: "Validé",
		},
	]);
</script>

<style scoped></style>
