<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<BackButton />

			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Détails</li>
				</ol>
			</nav>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button
					class="btn btn-outline-danger mr-4 border-2"
					type="button"
					@click="$router.push('/signalement')"
				>
					Signaler la voiture
				</button>
				<button class="btn btn-outline-violet" type="button">Ajouter à la liste noire</button>
			</div>
		</div>

		<div class="intro-y box mt-8 rounded-lg">
			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold">Informations sur le Véhicule</h2>
				<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Marque</p>
							<p class="text-dark font-medium text-sm">Berline</p>
						</div>
						<div class="mt-4">
							<p class="font-semibold leading-7">Pays</p>
							<p class="text-dark font-medium text-sm">Bénin</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Immatriculation</p>
							<p class="text-dark font-medium text-sm">00XY</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Numéro de châssis</p>
							<p class="text-dark font-medium text-sm">8392OIEHOH</p>
						</div>
					</div>
				</div>
			</div>

			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold">Information sur le propriétaire</h2>
				<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Nom et Prénom ou Raison social</p>
							<p class="text-dark font-medium text-sm">ADENIYI Ichola</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Téléphone</p>
							<p class="text-dark font-medium text-sm">+229 95 99 08 58</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Pays de naissance</p>
							<p class="text-dark font-medium text-sm">Bénin</p>
						</div>
					</div>
				</div>
			</div>

			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold">Information sur l’officier ayant enregistrer</h2>
				<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Nom et Prénoms</p>
							<p class="text-dark font-medium text-sm">KAKPO Brice</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="font-semibold leading-7">Matricule</p>
							<p class="text-dark font-medium text-sm">002121232</p>
						</div>
					</div>
				</div>
			</div>

			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold mb-8">Historique de passages</h2>
				<DataTable
					:has-actions="false"
					:has-header="false"
					:headers="headers"
					:items="data"
					empty-text="Aucun produit d'épargne trouvé"
					header-class="uppercase text-start"
					search
				>
					<template #statut="{ item }">
						<StatusComponent :status="item.statut" />
					</template>
				</DataTable>
			</div>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-primary mr-4 border-2 w-48" type="button">Imprimer</button>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import BackButton from "@/components/BackButton.vue";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Immatriculation", sortable: false },
		{ key: "owner", title: "Propriétaire", sortable: false },
		{ key: "owner_name", title: "Conducteur", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Passage", sortable: false },
	];

	const data = ref([
		{
			key: 1,
			reference: "REF-0001",
			owner_name: "John Doe",
			owner: "Roger Michelin",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 2,
			reference: "REF-0002",
			owner_name: "Jane Doe",
			owner: "Roger Michelin",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 3,
			reference: "REF-0003",
			owner_name: "John Doe",
			owner: "Roger Michelin",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
	]);
</script>

<style scoped></style>
