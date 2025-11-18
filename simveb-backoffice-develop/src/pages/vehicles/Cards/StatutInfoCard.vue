<template>
	<VCard class="side-card">
		<div class="card-inner is-one-third">
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Statut</span>
				</div>
				<div class="column is-flex is-justify-content-space-between">
					<VTag :label="demand.status_label" :color="statusColor(demand?.status)" />
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span class="has-text-weight-semibold">Référence</span>
				</div>
				<div class="column">{{ demand.reference }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Service</span>
				</div>
				<div class="column">
					<VTag :label="demand.service" color="primary" />
				</div>
			</div>

			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Initiateur</span>
				</div>
				<div class="column is-flex is-justify-content-space-between">
					<span>{{ author.name }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">NPI</span>
				</div>
				<div class="column">
					<span class="">{{ author.npi }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">ID Profil</span>
				</div>
				<div class="column">
					<span>{{ author.profile }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Date de création</span>
				</div>
				<div class="column">
					<span>{{ demand.created_at }}</span>
				</div>
			</div>
		</div>

		<div class="card-head mt-4">
			<h3>Vérification du dossier</h3>
		</div>
		<div class="card-inner is-one-third">
			<ul class="steps has-content-centered">
				<template v-for="(step, index) in steps" :key="index">
					<li :class="{ 'is-active': step.is_current }" class="steps-segment">
						<span class="steps-marker">
							<i class="fa-light" :class="{ 'fa-check': step.is_done }"></i>
						</span>
						<div class="steps-content">
							<p class="is-size-8">{{ step.label }}</p>
						</div>
					</li>
				</template>
			</ul>
		</div>
	</VCard>
</template>
<script setup lang="ts">
	import statusColor from "/src/utils/status-color";

	defineProps<{
		steps: any[];
		demand: any;
		author: any;
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

	.steps.has-content-centered {
		width: 100%;
		overflow-x: scroll;
		// customize the scroll bar
		scrollbar-width: thin;
		scrollbar-color: var(--primary) var(--white);
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
