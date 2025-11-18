<template>
	<div class="col-span-12 dashboard-card mt-4">
		<div class="intro-y flex items-center">
			<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-4">
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard
					background-color="bg-green-100"
					icon="floppy-disk"
					icon-color="lime-400"
					title="Véhicules enregistrés"
				>
					{{ stats.recorded_vehicles }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard background-color="bg-green-100" icon="rotate" icon-color="lime-400" title="Changements">
					{{ stats.changes }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard
					background-color="bg-green-100"
					icon="arrow-up-from-bracket"
					icon-color="lime-400"
					title="Rejets"
				>
					{{ stats.removal }}
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
