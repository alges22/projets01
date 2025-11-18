<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Demandes</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-semibold mr-auto">Sélectionner un type de service</h2>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5">
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Demande d'immatriculation"
					@click="$router.push('/parc/dashboard/immatriculation/VehiculeType')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/plaque.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Déclarer"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Déclaration de gage"
					@click="$router.push('/parc/dashboard/declaration/BankSelection')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7505033] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/hand-crosssing.png"
						/>
					</div>
				</CallToActionCard>
			</div>
		</div>

		<div class="mt-8 flex items-end">
			<div
				class="h-20 w-1/6 inline-flex text-center justify-center items-center border border-t-8 border-t-blue-600 bg-slate-50 hover:cursor-pointer font-bold"
			>
				Demande d’immatriculation
			</div>

			<div
				class="h-16 w-1/6 inline-flex text-center justify-center items-center border border-t-4 border-t-slate-300 bg-slate-50 hover:cursor-pointer"
			>
				Déclaration de gage
			</div>
		</div>

		<div class="grid grid-cols-12 gap-6 !rounded-none dashboard-card">
			<DataTable
				:headers="headers"
				:items="data"
				create-title="Créer un produit d'épargne"
				empty-text="Aucun produit d'épargne trouvé"
				header-class="uppercase text-start"
				search
			>
				<template #statut="{ item }">
					<StatusComponent :status="item.status" />
				</template>
				<template #actions>
					<a href="#">Voir détails</a>
				</template>
			</DataTable>
		</div>
	</div>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import { ref } from "vue";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import NotificationBox from "@/components/NotificationBox.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import CallToActionCard from "@/components/CallToActionCard.vue";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "vin", title: "Immatriculation", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const data = ref([
		{
			key: 1,
			reference: "RED0001",
			owner_name: "John Doe",
			vin: "VIN-0001",
			created_at: "2021-10-10 10:00:00",
			status: "pending",
		},
		{
			key: 2,
			reference: "REF-0002",
			owner_name: "Jane Doe",
			vin: "VIN-0002",
			created_at: "2021-10-10 10:00:00",
			status: "validated",
		},
		{
			key: 3,
			reference: "REF-0003",
			owner_name: "John Doe",
			vin: "VIN-0003",
			created_at: "2021-10-10 10:00:00",
			status: "rejected",
		},
		{
			key: 4,
			reference: "REF-0004",
			owner_name: "Jane Doe",
			vin: "VIN-0004",
			created_at: "2021-10-10 10:00:00",
			status: "failed",
		},
		{
			key: 5,
			reference: "REF-0005",
			owner_name: "John Doe",
			vin: "VIN-0005",
			created_at: "2021-10-10 10:00:00",
			status: "unpaid",
		},
		{
			key: 6,
			reference: "REF-0006",
			owner_name: "Jane Doe",
			vin: "VIN-0006",
			created_at: "2021-10-10 10:00:00",
			status: "non_payable",
		},
		{
			key: 7,
			reference: "REF-0007",
			owner_name: "John Doe",
			vin: "VIN-0007",
			created_at: "2021-10-10 10:00:00",
			status: "suspended",
		},
		{
			key: 8,
			reference: "REF-0008",
			owner_name: "Jane Doe",
			vin: "VIN-0008",
			created_at: "2021-10-10 10:00:00",
			status: "canceled",
		},
		{
			key: 9,
			reference: "REF-0009",
			owner_name: "John Doe",
			vin: "VIN-0009",
			created_at: "2021-10-10 10:00:00",
			status: "closed",
		},
		{
			key: 10,
			reference: "REF-0010",
			owner_name: "Jane Doe",
			vin: "VIN-0010",
			created_at: "2021-10-10 10:00:00",
			status: "refunded",
		},
	]);
</script>
