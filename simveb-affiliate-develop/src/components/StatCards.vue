<template>
	<template v-for="stat in stats" :key="stat.label">
		<div v-if="stat" class="col-sm-6 col-xl-3">
			<MetaCard :icon="stat.icon" icon-color="primary" :title="stat.title">
				<div class="d-flex align-items-end mt-2">
					<div v-if="loading" class="spinner-border" role="status">
						<span class="visually-hidden">Chargement...</span>
					</div>
					<h4 v-else class="mb-0 me-2">{{ stat.value }}</h4>
				</div>
				<small>{{ stat.label }}</small>
			</MetaCard>
		</div>
	</template>
</template>

<script setup>
	import MetaCard from "@/components/MetaCard.vue";

	defineProps({
		stats: {
			type: Array,
			default: () => [],
		},
		loading: {
			type: Boolean,
			default: false,
		},
	});
</script>

<style scoped></style>
