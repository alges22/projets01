<template>
	<div class="col-span-12 dashboard-card mt-4">
		<div class="intro-y flex items-center">
			<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-4">
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard background-color="bg-blue-100" icon="car" icon-color="primary" title="Passage">
					{{ stats.passages }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard
					background-color="bg-green-100"
					icon="arrow-down-to-square"
					icon-color="success"
					title="Entrée"
				>
					{{ stats.inputs }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard
					background-color="bg-yellow-100"
					icon="arrow-up-from-bracket"
					icon-color="warning"
					title="Sorties"
				>
					{{ stats.outputs }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard background-color="bg-red-100" icon="hexagon-exclamation" icon-color="danger" title="Alertes">
					{{ stats.alerts }}
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
