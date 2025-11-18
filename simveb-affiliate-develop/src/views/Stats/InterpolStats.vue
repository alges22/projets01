<template>
	<div class="col-span-12 dashboard-card mt-4">
		<div class="intro-y flex items-center">
			<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-4">
			<div class="col-span-12 sm:col-span-6 intro-y">
				<MetaCard background-color="bg-yellow-100" icon="hourglass" icon-color="warning" title="En attente">
					{{ stats.pending_vehicles }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 intro-y">
				<MetaCard
					background-color="bg-red-100"
					icon="hexagon-exclamation"
					icon-color="danger"
					title="Véhicles en liste noire"
				>
					{{ stats.blacklisted_vehicles }}
				</MetaCard>
			</div>
		</div>
	</div>
</template>

<script setup>
	import MetaCard from "@/components/MetaCard.vue";

	defineProps({
		stats: {
			type: Object,
			required: true,
		},
	});
</script>

<style scoped lang="scss"></style>
