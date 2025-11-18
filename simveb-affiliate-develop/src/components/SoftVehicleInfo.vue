<template>
	<div class="intro-y box mt-8 rounded-xl border" :class="'border-' + color">
		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<h2 class="text-lg leading-7 font-bold">Informations sur le Véhicule</h2>
			<div class="flex flex-col lg:flex-row px-4 pt-4 pb-0">
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Marque</p>
						<p class="text-dark font-medium text-sm">Berline</p>
					</div>
					<div class="mt-4">
						<p class="font-semibold leading-7" :class="'text-' + color">Pays</p>
						<p class="text-dark font-medium text-sm">Bénin</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Immatriculation</p>
						<p class="text-dark font-medium text-sm">{{ vehicleInfo.immatriculation }}</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Numéro de châssis</p>
						<p class="text-dark font-medium text-sm">{{ vehicleInfo.vin }}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<h2 class="text-lg leading-7 font-bold">Information sur le propriétaire</h2>
			<div class="flex flex-col lg:flex-row px-4 pt-4 pb-0">
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Nom et Prénom ou Raison social</p>
						<p class="text-dark font-medium text-sm">
							{{ vehicleOwner.lastname }} {{ vehicleOwner.firstname }}
						</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Téléphone</p>
						<p class="text-dark font-medium text-sm">{{ vehicleOwner.telephone }}</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Pays de naissance</p>
						<p class="text-dark font-medium text-sm">{{ vehicleOwner.birth_place }}</p>
					</div>
				</div>
			</div>
		</div>
		<div
			v-if="vehicleRegisterOfficer"
			class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400"
		>
			<h2 class="text-lg leading-7 font-bold">Information sur l’officier ayant enregistrer</h2>
			<div class="flex flex-col lg:flex-row px-4 pt-4 pb-0">
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">Nom et Prénoms</p>
						<p class="text-dark font-medium text-sm">
							{{ vehicleRegisterOfficer.firstname }} {{ vehicleRegisterOfficer.lastname }}
						</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7" :class="'text-' + color">NPI</p>
						<p class="text-dark font-medium text-sm">{{ vehicleRegisterOfficer.npi }}</p>
					</div>
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
		vehicleOwner: {
			type: Object,
			required: true,
		},
		vehicleRegisterOfficer: {
			type: Object,
			required: false,
		},
		color: {
			type: String,
			required: false,
			default: "success",
		},
	});
</script>
