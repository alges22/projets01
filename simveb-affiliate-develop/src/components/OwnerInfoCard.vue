<template>
	<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
		<div class="intro-y flex items-center">
			<h2 class="text-primary text-2xl">{{ title }}</h2>
		</div>
	</div>
	<div class="col-span-12 grid grid-cols-12 gap-6">
		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Nom</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.lastname }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Prénoms</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.firstname }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Date de naissance</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">
					{{ date(ownerInfo.birthdate).format("DD/MM/YYYY") }}
				</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Lieu de naissance</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.birth_place }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Nationalité</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.origin_country }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Sexe</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.gender }}</p>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">NPI</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.npi }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Référence du BFU</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.bfu }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Téléphone</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.telephone }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Adresse email</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.email }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Domicile</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ ownerInfo.address }}</p>
			</div>
		</div>
	</div>
</template>
<script setup>
	import { useDate } from "vue3-dayjs-plugin/useDate.js";

	defineProps({
		ownerInfo: {
			type: Object,
			required: true,
		},
		title: {
			type: String,
			required: false,
			default: "Informations du propriétaire",
		},
	});

	const date = useDate();
</script>
