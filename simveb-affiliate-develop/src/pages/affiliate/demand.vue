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
					@click="$router.push('/affiliate/dashboard/immatriculation/VehiculeType')"
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
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Demande de Ré-immatriculation"
					@click="$router.push('/affiliate/dashboard/reimmatriculation/OwnerInfo')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#795D6833] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/reimat.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Demande d’immatriculation de prestige"
					@click="$router.push('/affiliate/dashboard/immatriculation/PrestigeServiceType')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#FF474733] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/prestige.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Dépôt ou une reprise de titres"
					@click="$router.push('/affiliate/dashboard/reprise/OwnerInfo')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/reprise.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Transformation de véhicule"
					@click="$router.push('/affiliate/dashboard/transformation/CarChoice')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#008CCF33] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/arrow_crossing.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Demande de mutation"
					@click="$router.push('/affiliate/dashboard/mutation/OwnerInfo')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#00AFB533] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/arrow_bue_green.png"
						/>
					</div>
				</CallToActionCard>
			</div>
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					button-text="Demander"
					image-alt="Cliquez pour choisir la demande d'immatriculation"
					subtitle="Petite description"
					title="Vente"
					@click="$router.push('/affiliate/dashboard/vente/OwnerInfo')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7506E33] rounded-full p-2">
						<img
							alt="Cliquez pour choisir la demande d'immatriculation"
							class=""
							src="../../assets/images/parc/car_coin.png"
						/>
					</div>
				</CallToActionCard>
			</div>
		</div>

		<div class="grid grid-cols-12 gap-6 !rounded-none dashboard-card">
			<div class="intro-y mt-8 flex items-center col-span-12">
				<h2 class="mr-auto text-lg font-bold">Dossiers Récents</h2>
			</div>
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
		{ key: "owner_name", title: "Nom du dossier", sortable: false },
		{ key: "motif", title: "Motif", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const data = ref([
		{
			key: 1,
			reference: "RED0001",
			owner_name: "John Doe",
			motif: "Immatriculation",
			created_at: "2021-10-10 10:00:00",
			status: "validated",
		},
		{
			key: 2,
			reference: "REF-0002",
			owner_name: "Jane Doe",
			motif: "Re-immatriculation",
			created_at: "2021-10-10 10:00:00",
			status: "validated",
		},
		{
			key: 3,
			reference: "REF-0003",
			owner_name: "John Doe",
			motif: "Transformation",
			created_at: "2021-10-10 10:00:00",
			status: "validated",
		},
		{
			key: 4,
			reference: "REF-0004",
			owner_name: "Jane Doe",
			motif: "Dépôt / Reprise",
			created_at: "2021-10-10 10:00:00",
			status: "validated",
		},
		{
			key: 5,
			reference: "REF-0005",
			owner_name: "John Doe",
			motif: "Imm. Prestige",
			created_at: "2021-10-10 10:00:00",
			status: "pending",
		},
		{
			key: 6,
			reference: "REF-0006",
			owner_name: "Jane Doe",
			motif: "Vente",
			created_at: "2021-10-10 10:00:00",
			status: "pending",
		},
		{
			key: 7,
			reference: "REF-0007",
			owner_name: "John Doe",
			motif: "Mutation",
			created_at: "2021-10-10 10:00:00",
			status: "rejected",
		},
	]);
</script>
