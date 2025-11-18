<template>
	<VCard class="side-card">
		<div class="card-head">
			<h3 class="font-bold text-light">Détails du véhicule</h3>
		</div>
		<div class="card-inner is-one-third">
			<div class="columns">
				<div class="column">
					<span>VIN</span>
				</div>
				<div class="column">
					<KeyField :value="vehicle.vin" />
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Pays d'origine du véhicule</span>
				</div>
				<div class="column">
					<span>{{ vehicle.origin_country }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Marque</span>
				</div>
				<div class="column">
					<span>{{ vehicle.vehicle_brand }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Modèle de véhicule</span>
				</div>
				<div class="column">
					<span>{{ vehicle.vehicle_model }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Numéro Moteur</span>
				</div>
				<div class="column">
					<span>{{ vehicle.engin_number }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Parc d'achat du véhicule</span>
				</div>
				<div class="column">
					<span>{{ vehicle.park }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Année de 1ère mise en circulation</span>
				</div>
				<div class="column">
					<span>{{ vehicle.first_circulation_year }}</span>
				</div>
			</div>
		</div>

		<div class="card-head bg-primary-100 mt-8">
			<h3 class="!text-primary-dark">Caractéristiques du véhicule</h3>
		</div>
		<div class="card-inner is-one-third">
			<div v-for="(caracteristiq, index) in vehicle.characteristics" :key="index" class="columns">
				<div class="column">
					<span>{{ Object.keys(caracteristiq)[0] }}</span>
				</div>
				<div class="column">
					<span>{{ Object.values(caracteristiq)[0] }}</span>
				</div>
			</div>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	defineProps<{
		vehicle: any;
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
