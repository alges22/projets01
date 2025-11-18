<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner le type de service</h2>
	</div>

	<ServicesList is-form />
</template>

<script setup>
	import ServicesList from "@/views/global/ServicesList.vue";
</script>
