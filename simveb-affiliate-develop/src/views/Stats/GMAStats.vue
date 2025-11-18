<template>
	<div class="col-span-12 dashboard-card mt-4">
		<div class="intro-y flex items-center">
			<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-4">
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard
					title="Enregistrements validés"
					background-color="bg-marron-100"
					icon="car"
					icon-color="marron"
				>
					<template #default>{{ stats.recorded_vehicles.validated }}</template>
					<template #footer>
						<div class="text-marron-800 font-bold">
							<i class="fa-light fa-arrow-trend-up text-marron-800 me-2"></i>
							+2,2 % cette semaine
						</div>
					</template>
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard
					title="Enregistrements en cours"
					background-color="bg-marron-100"
					icon="car"
					icon-color="marron"
				>
					<template #default>{{ stats.recorded_vehicles.pending }}</template>
					<template #footer>
						<div class="text-marron-800 font-bold">
							<i class="fa-light fa-arrow-trend-up text-marron-800 me-2"></i>
							+2,2 % cette semaine
						</div>
					</template>
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard background-color="bg-marron-100" icon="arrows-rotate" icon-color="marron" title="Changements">
					<template #default>{{ stats.changes }}</template>
					<template #footer>
						<div class="text-marron-800 font-bold">
							<i class="fa-light fa-arrow-trend-down text-marron-800 me-2"></i>
							-1,8 % cette semaine
						</div>
					</template>
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
				<MetaCard background-color="bg-marron-100" icon="id-card" icon-color="marron" title="Retrait">
					<template #default>{{ stats.removal }}</template>
					<template #footer>
						<div class="text-marron-800 font-bold">
							<i class="fa-light fa-arrow-trend-up text-marron-800 me-2"></i>
							+5,2 % cette semaine
						</div>
					</template>
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
