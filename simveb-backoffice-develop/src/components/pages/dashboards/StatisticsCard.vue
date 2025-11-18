<template>
	<h4 class="dark-inverted">Les statistiques</h4>

	<div class="quick-stats">
		<VLoader :active="loading">
			<div class="quick-stats-inner">
				<div v-if="can('browse-immatriculation-demand-stats')" class="quick-stat">
					<VBlock
						:title="immatriculationStats.all?.toString()"
						subtitle="Total demandes d'immatriculations"
						center
						m-responsive
						t-responsive
					>
						<template #icon>
							<VIconBox color="purple" rounded>
								<i aria-hidden="true" class="fa-light fa-analytics-alt-1"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>

				<div v-if="can('browse-duplicate-demand-stats')" class="quick-stat">
					<VBlock
						:title="duplicateStats.plate?.toString()"
						subtitle="Duplicata de plaques"
						center
						m-responsive
						t-responsive
					>
						<template #icon>
							<VIconBox color="orange" rounded>
								<i class="fa-light fa-copy" aria-hidden="true"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>

				<div v-if="can('browse-duplicate-demand-stats')" class="quick-stat">
					<VBlock
						:title="duplicateStats.gray_card?.toString()"
						subtitle="Duplicata Carte grise"
						center
						m-responsive
						t-responsive
					>
						<template #icon>
							<VIconBox color="green" rounded>
								<i class="fa-light fa-credit-card" aria-hidden="true"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>

				<div v-if="can('stats-plate')" class="quick-stat">
					<VBlock :title="plateStats.total?.toString()" subtitle="Plaques" center m-responsive t-responsive>
						<template #icon>
							<VIconBox color="info" rounded>
								<i class="fa-light fa-package" aria-hidden="true"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>
			</div>
		</VLoader>
	</div>
</template>

<script setup lang="ts">
	import client from "/@src/composable/axiosClient";
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();
	const immatriculationStats = ref([]);
	const duplicateStats = ref([]);
	const plateStats = ref([]);
	const loading = ref(true);

	onMounted(() => {
		client({
			method: "GET",
			url: "/stats",
		})
			.then((response) => {
				immatriculationStats.value = response.data.immatriculation_demand;
				duplicateStats.value = response.data.duplicate_demand;
				plateStats.value = response.data.plate;
			})
			.finally(() => {
				loading.value = false;
			});
	});
</script>

<style scoped lang="scss"></style>
