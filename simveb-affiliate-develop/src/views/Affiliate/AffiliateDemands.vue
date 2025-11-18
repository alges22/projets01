<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner un type de service</h2>
	</div>

	<ServicesList />

	<div class="grid grid-cols-12 gap-6 !rounded-none mt-4">
		<div class="intro-y mt-4 flex items-center col-span-12">
			<h2 class="mr-auto text-lg font-bold">Dossiers Récents</h2>
		</div>

		<ImmatriculationDemandList />
	</div>
</template>

<script setup>
	import ServicesList from "@/views/global/ServicesList.vue";
	import ImmatriculationDemandList from "@/views/global/ImmatriculationDemandList.vue";
</script>

<style scoped></style>
