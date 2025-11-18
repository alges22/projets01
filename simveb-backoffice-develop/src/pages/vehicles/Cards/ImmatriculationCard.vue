<template>
	<div class="column is-half">
		<div class="mb-2 is-flex is-justify-content-center is-uppercase">
			<span class="has-text-primary">Avant</span>
		</div>
		<square-plate-view
			v-if="demandInfo.model.front_plate_shape.code == 'square'"
			:bg-color="demandInfo.model.plate_color.color_code"
			:text-color="demandInfo.model.plate_color.text_color"
			:immatriculation-number="demandInfo.model.label ?? demandInfo.model.number"
		/>
		<rectangle-plate-view
			v-else-if="demandInfo.model.front_plate_shape.code == 'rectangle'"
			:bg-color="demandInfo.model.plate_color.color_code"
			:text-color="demandInfo.model.plate_color.text_color"
			:immatriculation-number="demandInfo.model.label ?? demandInfo.model.number"
		/>
	</div>
	<div v-if="demandInfo.vehicle.nb_plate > 1" class="column is-half">
		<div class="mb-2 is-flex is-justify-content-center is-uppercase">
			<span class="has-text-primary">Arrière</span>
		</div>
		<square-plate-view
			v-if="demandInfo.model.back_plate_shape.code == 'square'"
			:bg-color="demandInfo.model.plate_color.color_code"
			:text-color="demandInfo.model.plate_color.text_color"
			:immatriculation-number="demandInfo.model.label ?? demandInfo.model.number"
		/>
		<rectangle-plate-view
			v-else-if="demandInfo.model.back_plate_shape.code == 'rectangle'"
			:bg-color="demandInfo.model.plate_color.color_code"
			:text-color="demandInfo.model.plate_color.text_color"
			:immatriculation-number="demandInfo.model.label ?? demandInfo.model.number"
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
</template>

<script setup lang="ts">
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
