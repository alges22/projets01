<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Certificats</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="mt-20 flex items-end">
			<div
				class="h-20 w-1/6 inline-flex justify-center items-center border border-t-8 border-t-blue-600 bg-slate-50 hover:cursor-pointer"
			>
				Certificat de cession
			</div>

			<div
				class="h-16 w-1/6 inline-flex justify-center items-center border border-t-4 border-t-slate-300 bg-slate-50 hover:cursor-pointer"
				@click="$router.push('/affiliate/gages')"
			>
				Gages
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

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "N°Dossier", sortable: false },
		{ key: "old_owner", title: "Ancien Propriétaire", sortable: false },
		{ key: "new_owner_name", title: "Nouveau Propriétaire", sortable: false },
		{ key: "vin", title: "Immatriculation", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: false },
	];

	const data = ref([
		{
			key: 1,
			reference: "RED0001",
			old_owner: "John Doe",
			new_owner_name: "John Doe",
			vin: "VIN-0001",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 2,
			reference: "REF-0002",
			old_owner: "Jane Doe",
			new_owner_name: "Jane Doe",
			vin: "VIN-0002",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 3,
			reference: "REF-0003",
			old_owner: "John Doe",
			new_owner_name: "John Doe",
			vin: "VIN-0003",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 4,
			reference: "REF-0004",
			old_owner: "Jane Doe",
			new_owner_name: "Jane Doe",
			vin: "VIN-0004",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 5,
			reference: "REF-0005",
			old_owner: "John Doe",
			new_owner_name: "John Doe",
			vin: "VIN-0005",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 6,
			reference: "REF-0006",
			old_owner: "Jane Doe",
			new_owner_name: "Jane Doe",
			vin: "VIN-0006",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 7,
			reference: "REF-0007",
			old_owner: "John Doe",
			new_owner_name: "John Doe",
			vin: "VIN-0007",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 8,
			reference: "REF-0008",
			old_owner: "Jane Doe",
			new_owner_name: "Jane Doe",
			vin: "VIN-0008",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 9,
			reference: "REF-0009",
			old_owner: "John Doe",
			new_owner_name: "John Doe",
			vin: "VIN-0009",
			created_at: "2021-10-10 10:00:00",
		},
		{
			key: 10,
			reference: "REF-0010",
			old_owner: "Jane Doe",
			new_owner_name: "Jane Doe",
			vin: "VIN-0010",
			created_at: "2021-10-10 10:00:00",
		},
	]);
</script>

<style scoped></style>
