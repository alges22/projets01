<template>
	<div class="columns tab-details-inner">
		<div class="column is-12">
			<VCard class="side-card">
				<div class="card-head">
					<h3>Plaque d'immatriculation</h3>
				</div>
				<AlertComponent
					v-if="!demandInfo.model.label && !demandInfo.model.number"
					class="border-l-4 my-4 rounded-md"
					color="yellow"
					icon="exclamation-circle"
				>
					<p class="text-sm text-yellow-700">
						L'immatriculation n'a pas encore été générée. Ceci est une prévisualisation.
					</p>
				</AlertComponent>
				<div class="card-inner is-multiline columns">
					<div class="column is-half">
						<div class="mb-2 is-flex is-uppercase">
							<span class="has-text-primary">Arrière</span>
						</div>
						<NewImmatriculationPlateView
							:bg-color="demandInfo.model.plate_color.color_code"
							:text-color="demandInfo.model.plate_color.text_color"
							:form="demandInfo.model.back_plate_shape.code"
							:immatriculation-number="
								demandInfo.model.label ?? demandInfo.model.number ?? ['', 'XX', 'XX', '1234', 'BJ']
							"
							:is-label="!!demandInfo.model.label"
							:country-code="demandInfo.model.country_code"
						/>
					</div>
					<div v-if="demandInfo.vehicle.nb_plate > 1" class="column is-half">
						<div class="mb-2 is-flex is-uppercase">
							<span class="has-text-primary">Avant</span>
						</div>
						<NewImmatriculationPlateView
							:bg-color="demandInfo.model.plate_color.color_code"
							:text-color="demandInfo.model.plate_color.text_color"
							:form="demandInfo.model.front_plate_shape.code"
							:immatriculation-number="
								demandInfo.model.label ?? demandInfo.model.number ?? ['', 'XX', 'XX', '1234', 'BJ']
							"
							:is-label="!!demandInfo.model.label"
							:country-code="demandInfo.model.country_code"
						/>
					</div>
					<div class="column is-12 mt-2">
						<div class="is-flex is-justify-content-space-between">
							<div class="is-flex is-flex-wrap-wrap align-items is-align-content-center">
								<span>
									Immatriculation:
									{{ demandInfo.model.label ?? demandInfo.model.number_label }}
								</span>
							</div>
						</div>
					</div>
				</div>
			</VCard>
		</div>
	</div>
</template>

<script setup lang="ts">
	import AlertComponent from "/@src/components/AlertComponent.vue";

	defineProps<{
		demandInfo: any;
	}>();
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	@media only screen and (width <=767px) {
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: portrait) {
	}
</style>
