<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Vérification des immatriculations</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="intro-y flex items-center mt-8 lg:mt-16">
			<h2 class="text-lg font-semibold mr-auto">Vérifier un véhicule</h2>
		</div>

		<div class="flex justify-between items-end dashboard-card !rounded-md mt-4">
			<div class="w-full">
				<label class="form-label" for="crud-form-1">Entrer le numéro d'immatriculation de la voiture</label>
				<input id="crud-form-1" class="form-control w-full" placeholder="Input text" type="text" value="00XY" />
			</div>
			<div class="ms-2 w-1/5">
				<button
					class="btn btn-primary w-full h-auto"
					type="button"
					@click="$router.push('/police/verification/VehicleInfo')"
				>
					Vérifier
				</button>
			</div>
		</div>

		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-semibold mr-auto">Enregistrer un véhicule</h2>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5">
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Regroupe les voitures"
					title="Voiture local"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="@/assets/images/parc/car_green.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					background-color="bg-[#27ae6033]"
					button-text="Déclarer"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Regroupe les voitures"
					title="Voiture étranger"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7505033] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="@/assets/images/parc/car_red.png"
						/>
					</div>
				</CallToActionCard>
			</div>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
			<div class="intro-y mt-8 flex items-center col-span-12">
				<h2 class="mr-auto text-lg font-bold">Passages récents</h2>
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
					<router-link class="text-primary" to="/police/verification/CarDetails">Voir détails</router-link>
				</template>
			</DataTable>
		</div>
	</div>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import NotificationBox from "@/components/NotificationBox.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { ref } from "vue";
	import CallToActionCard from "@/components/CallToActionCard.vue";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Immatriculation", sortable: false },
		{ key: "vin", title: "Véhicule", sortable: false },
		{ key: "owner_name", title: "Conducteur", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Passage", sortable: false },
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
</script>

<style scoped></style>
