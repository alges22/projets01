<template>
	<VCard class="side-card">
		<div class="card-head">
			<h3>Cycle de vie du véhicule</h3>
		</div>
		<div class="card-inner is-one-third pt-1 mx-4 mt-2" style="font-size: 12px">
			<div v-if="demands.length == 0" class="has-text-centered has-text-weight-bold">Aucune activité</div>
			<div v-for="demand in demands" :key="demand.id" class="mt-4">
				<div class="columns">
					<div class="column is-half has-text-weight-bold" style="background: #d4e4fb; color: #1470eb">
						<span class="">{{ demand.service.name }}</span>
					</div>

					<div class="column is-half has-text-weight-bold" style="background: #cee0fa; color: #1470eb">
						<span class="has-text-weight-semibold">
							{{ dayjs(demand.created_at).format("DD/MM/YYYY") }}
						</span>
					</div>
				</div>
				<div class="columns">
					<div class="column is-half" style="background: #f6f9fe">
						<span class="">Dossier Ref</span>
					</div>

					<div class="column is-half has-text-weight-semibold" style="background: #f6f9fe">
						<span class="has-text-weight-semibold"># {{ demand.reference }}</span>
					</div>
				</div>
			</div>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	import { useDate } from "vue3-dayjs-plugin/useDate";

	defineProps<{
		demands: any;
	}>();

	const dayjs = useDate();
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.card-head {
		display: flex;
		align-items: center;
		justify-content: space-between;
		//margin-bottom: 20px;
		background: var(--primary);
		padding: 20px;
		border-radius: 8px;

		h3 {
			font-family: var(--font-alt);
			font-size: 1.2rem;
			font-weight: 700;
			color: var(--white);
			line-height: 1.2;
			transition: all 0.3s; // transition-all test
		}
	}

	.side-card {
		@include vuero-s-card;

		padding: 40px;
		margin-bottom: 1.5rem;

		h4 {
			font-family: var(--font-alt);
			font-weight: 600;
			font-size: 0.8rem;
			text-transform: uppercase;
			color: var(--primary);
			margin-bottom: 16px;
		}
	}

	@media only screen and (width <=767px) {
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: portrait) {
		.columns {
			display: flex;

			.column {
				min-width: 50%;
			}
		}
	}
</style>
