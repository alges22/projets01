<template>
	<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
		<h2 class="text-lg leading-7 font-bold">Informations sur le Véhicule</h2>
		<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
			<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
				<div>
					<p :class="`font-semibold leading-7 ${color}`">Marque</p>
					<p class="text-dark font-medium text-sm">{{ vehicleInfo?.brand }}</p>
				</div>
				<div class="mt-4">
					<p :class="`font-semibold leading-7 ${color}`">Pays</p>
					<p class="text-dark font-medium text-sm">{{ vehicleInfo?.country }}</p>
				</div>
			</div>
			<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
				<div>
					<p :class="`font-semibold leading-7 ${color}`">Immatriculation</p>

					<p class="text-dark font-medium text-sm">{{ vehicleInfo?.immatriculation_number }}</p>
				</div>
			</div>
			<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
				<div>
					<p :class="`font-semibold leading-7 ${color}`">Numéro de châssis</p>
					<p class="text-dark font-medium text-sm">{{ vehicleInfo?.vin }}</p>
				</div>
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
		color: {
			type: String,
			required: false,
			default: "",
		},
	});
</script>
