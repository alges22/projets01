<template>
	<div class="col-span-12 dashboard-card mt-4">
		<div class="intro-y flex items-center">
			<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-4">
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard background-color="bg-green-100" icon="check" icon-color="success" title="Terminées">
					{{ stats.validated }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard background-color="bg-yellow-100" icon="hourglass-end" icon-color="warning" title="En cours">
					{{ stats.pending }}
				</MetaCard>
			</div>
			<div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
				<MetaCard background-color="bg-red-100" icon="hand" icon-color="danger" title="Rejets">
					{{ stats.rejected }}
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
