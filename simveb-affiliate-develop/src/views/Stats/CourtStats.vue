<template>
	<div class="col-span-12 dashboard-card mt-4">
		<div class="intro-y flex items-center">
			<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-4">
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard
					background-color="bg-white"
					icon="hand-pointer"
					icon-color="primary-dark"
					title="Demandes de mise en gage"
				>
					{{ stats.pledges.validated }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard
					background-color="bg-white"
					icon="hand-pointer"
					icon-color="primary-dark"
					title="Demandes de mise en gage rejetées"
				>
					{{ stats.pledges.canceled }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard
					background-color="bg-white"
					icon="arrows-cross"
					icon-color="primary-dark"
					title="Mises en opposition"
				>
					{{ stats.oppositions }}
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
