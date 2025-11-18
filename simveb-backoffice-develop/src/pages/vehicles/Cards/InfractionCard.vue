<template>
	<VCard class="side-card">
		<div class="card-head">
			<h3>Infractions</h3>
		</div>
		<div class="card-inner is-one-third">
			<div v-if="alerts.length == 0" class="has-text-centered has-text-weight-bold">Aucune infraction</div>
			<div v-for="alert in alerts" :key="alert.id">
				<div v-for="alert_type in alert.alert_types" :key="alert_type.id" class="columns">
					<div class="column is-half">
						<span class="has-text-weight-semibold">{{ alert_type.name }}</span>
					</div>
					<div class="column">{{ alert.recorded_at }}</div>
				</div>
			</div>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	defineProps<{
		alerts: any;
	}>();
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
