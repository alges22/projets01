<template>
	<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
		<h2 class="text-lg leading-7 font-bold">Information sur le propriétaire</h2>
		<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
			<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
				<div>
					<p :class="`font-semibold leading-7  ${color}`">Nom et Prénom ou Raison social</p>
					<p class="text-dark font-medium text-sm">{{ `${ownerInfo?.lastname} ${ownerInfo?.firstname}` }}</p>
				</div>
			</div>
			<!-- <div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
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
			</div> -->
		</div>
	</div>
</template>
<script setup>
	defineProps({
		ownerInfo: {
			type: Object,
			required: true,
		},
		loading: {
			type: Boolean,
			required: false,
			default: false,
		},
		color: {
			type: String,
			required: false,
			default: "",
		},
	});
	import { ref } from "vue";

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
