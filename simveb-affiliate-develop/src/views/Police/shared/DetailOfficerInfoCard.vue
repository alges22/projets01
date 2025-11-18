<template>
	<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
		<h2 class="text-lg leading-7 font-bold">Information sur l’officier ayant enregistrer</h2>
		<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
			<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
				<div>
					<p class="font-semibold leading-7">Nom et Prénoms</p>
					<p class="text-dark font-medium text-sm">
						{{ `${officerInfo?.name}` }}
					</p>
				</div>
			</div>
			<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
				<div>
					<p class="font-semibold leading-7">Matricule</p>
					<p class="text-dark font-medium text-sm">{{ officerInfo?.matricule }}</p>
				</div>
			</div>
		</div>
	</div>
</template>
<script setup>
	defineProps({
		officerInfo: {
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
