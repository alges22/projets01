<template>
	<VCard class="side-card">
		<div class="card-head">
			<h3 class="font-bold text-light">{{ title }}</h3>
		</div>
		<div class="card-inner is-one-third">
			<template v-if="user?.ifu">
				<div class="columns">
					<div class="column">
						<span>Nom</span>
					</div>
					<div class="column">
						<span>{{ user.social_reason }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>IFU</span>
					</div>
					<div class="column">
						<span>{{ user.ifu }}</span>
					</div>
				</div>
			</template>
			<template v-else-if="user?.npi">
				<div class="columns">
					<div class="column">
						<span>Nom</span>
					</div>
					<div class="column">
						<span>{{ user.lastname }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Prénoms</span>
					</div>
					<div class="column">
						<span>{{ user.firstname }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>NPI</span>
					</div>
					<div class="column">
						<span>{{ user.npi }}</span>
					</div>
				</div>
			</template>
			<div class="columns">
				<div class="column">
					<span>Téléphone</span>
				</div>
				<div class="column">
					<span>{{ user.telephone }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Email</span>
				</div>
				<div class="column">
					<span>{{ user.email }}</span>
				</div>
			</div>
		</div>
	</VCard>
</template>
<script setup lang="ts">
	defineProps({
		user: {
			type: Object,
			required: true,
		},
		title: {
			type: String,
			required: true,
		},
	});
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
