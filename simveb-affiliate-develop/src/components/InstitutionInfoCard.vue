<template>
	<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
		<div class="intro-y flex items-center">
			<h2 class="text-primary text-2xl">{{ title }}</h2>
		</div>
	</div>
	<div class="col-span-12 grid grid-cols-12 gap-6">
		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Sigle</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.acronym }}</p>
			</div>
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Nom</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.name }}</p>
			</div>
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Type</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.type?.name }}</p>
			</div>
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Adresse mail</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.email }}</p>
			</div>
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Numéro IFU</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.ifu }}</p>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Logo</p>
				<img
					v-if="institutionInfo.logo_path"
					:src="institutionInfo.logo_path"
					class="font-bold text-dark w-1/2 text-lg leading-5"
					alt=""
				/>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Téléphone</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.telephone }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Date de création</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">
					{{ date(institutionInfo.created_at).format("DD/MM/YYYY") }}
				</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Adresse</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ institutionInfo.address }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Commune</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">
					{{ institutionInfo.town?.name }} | {{ institutionInfo.district?.name }} |
					{{ institutionInfo.village?.name }}
				</p>
			</div>
		</div>
	</div>
</template>
<script setup>
	import { useDate } from "vue3-dayjs-plugin/useDate.js";

	defineProps({
		institutionInfo: {
			type: Object,
			required: true,
		},
		title: {
			type: String,
			required: false,
			default: "Informations de l'institution",
		},
	});

	const date = useDate();
</script>
