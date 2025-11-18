<template>
	<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
		<div class="intro-y flex items-center">
			<h2 class="text-primary text-2xl">Informations du Véhicule</h2>
		</div>
	</div>
	<div class="col-span-12 grid grid-cols-12 gap-6">
		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Pays d'origine du véhicule</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.origin_country }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Numéro du châssis</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.vin }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Marque</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.vehicle_brand }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Modèle de véhicule</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.vehicle_model }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Images</p>
				<template v-for="(image, index) in vehicleInfo.images" :key="index">
					<img
						height="50"
						width="50"
						:alt="'Image Impression N°' + index + 1"
						:src="image.url"
						data-action="zoom"
					/>
				</template>
			</div>
		</div>
		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div class="flex items-center pb-2 mt-2 border-b">
				<p class="w-1/2 text-lg leading-5">Numéro Moteur</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.engin_number }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Parc d'achat du véhicule</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.park }}</p>
			</div>
			<div class="flex items-center pb-2 mt-6 border-b">
				<p class="w-1/2 text-lg leading-5">Année de 1ère mise en circulation</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicleInfo.first_circulation_year }}</p>
			</div>
		</div>
	</div>
	<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
		<div class="intro-y flex items-center">
			<h2 class="text-primary text-2xl">Caractéristiques du Véhicule</h2>
		</div>
	</div>
	<div v-if="vehicleInfo.characteristics?.length > 0" class="col-span-12 grid grid-cols-12 gap-6">
		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div
				v-for="(caracteristiq, index) in vehicleInfo.characteristics.slice(
					0,
					Math.ceil(vehicleInfo.characteristics.length / 2)
				)"
				:key="index"
				class="flex items-center pb-2 mt-2 border-b"
			>
				<p class="w-1/2 text-lg leading-5">{{ Object.keys(caracteristiq)[0] }}</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ Object.values(caracteristiq)[0] }}</p>
			</div>
		</div>
		<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
			<div
				v-for="(caracteristiq, index) in vehicleInfo.characteristics.slice(
					Math.ceil(vehicleInfo.characteristics.length / 2),
					vehicleInfo.characteristics.length
				)"
				:key="index"
				class="flex items-center pb-2 mt-2 border-b"
			>
				<p class="w-1/2 text-lg leading-5">{{ Object.keys(caracteristiq)[0] }}</p>
				<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ Object.values(caracteristiq)[0] }}</p>
			</div>
		</div>
	</div>
</template>
<script setup>
	defineProps({
		vehicleInfo: {
			type: Object,
			required: true,
		},
		loading: {
			type: Boolean,
			required: false,
			default: false,
		},
	});
</script>
