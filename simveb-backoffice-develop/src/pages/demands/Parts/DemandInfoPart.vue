<template>
	<div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4 tab-details-inner">
		<StatutInfoCard
			:author="demandInfo.author"
			:demand="demandInfo.demand"
			:steps="demandInfo.steps"
			:treatment="demandInfo.active_treatment"
		/>

		<VCard class="side-card">
			<div class="card-inner is-one-third">
				<div class="columns">
					<div class="column">
						<span>N° de paiement</span>
					</div>
					<div class="column">
						<KeyField :value="demandInfo.order.reference" />
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>N° de facture</span>
					</div>
					<div class="column">
						<KeyField :value="demandInfo.order.invoice_reference" />
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Propriétaire</span>
					</div>
					<div class="column">
						<span>
							{{ demandInfo.vehicle_owner.firstname }}
							{{ demandInfo.vehicle_owner.lastname }}
						</span>
					</div>
				</div>
				<div v-if="demandInfo.model.front_plate_shape" class="columns">
					<div class="column">
						<span>Forme plaque avant</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.model.front_plate_shape.name }}</span>
					</div>
				</div>
				<div v-if="demandInfo.model.back_plate_shape" class="columns">
					<div class="column">
						<span>Forme plaque arrière</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.model.back_plate_shape.name }}</span>
					</div>
				</div>
				<div v-if="demandInfo.model.plate_color" class="columns">
					<div class="column">
						<span>Couleur de plaque</span>
					</div>
					<div class="column">
						<span
							:style="{ backgroundColor: demandInfo.model.plate_color?.color_code }"
							class="color-box"
						></span>
					</div>
				</div>
			</div>

			<div class="card-head bg-primary-100 mt-8">
				<h3 class="!text-primary-dark">Propriétaire</h3>
			</div>
			<div class="card-inner is-one-third">
				<div class="columns">
					<div class="column">
						<span>Nom</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.vehicle_owner.lastname }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Prénoms</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.vehicle_owner.firstname }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Téléphone</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.vehicle_owner.telephone }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>NPI</span>
					</div>
					<div class="column">
						<KeyField :value="demandInfo.vehicle_owner.npi" />
					</div>
				</div>
			</div>

			<div class="card-head bg-primary-100 mt-8">
				<h3 class="!text-primary-dark">Pièces jointes</h3>
			</div>
			<div class="card-inner is-one-third">
				<FileList :files="demandInfo.files" />
			</div>
		</VCard>

		<VehicleInfoCard :vehicle="demandInfo.vehicle" />
	</div>
</template>

<script lang="ts" setup>
	import StatutInfoCard from "/@src/pages/demands/Cards/StatutInfoCard.vue";
	import VehicleInfoCard from "/@src/pages/demands/Cards/VehicleInfoCard.vue";
	import FileList from "/@src/pages/demands/Cards/FileList.vue";

	defineProps<{
		demandInfo: any;
	}>();
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.tab-details-inner {
		flex-wrap: wrap !important;
	}

	.color-box {
		width: 35px;
		height: 35px;
		border-radius: 15%;
		margin-left: 10px;
	}

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
