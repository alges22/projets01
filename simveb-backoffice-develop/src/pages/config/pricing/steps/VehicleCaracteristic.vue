<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useServiceStore } from "/@src/stores/modules/service";

	const props = defineProps({
		serviceId: {
			type: String,
			default: null,
		},
	});
	const crudStore = useCrudStore();
	const serviceStore = useServiceStore();
	const { vehicleCaracteristicCategories, vehicleCaracteristicCategoryModelType } = storeToRefs(serviceStore);
	const update = ref(false);
	const { url, formLoading, errors, row: service, loading } = storeToRefs(crudStore);

	onMounted(() => {
		if (props.serviceId) {
			update.value = true;
			crudStore.fetchRow(props.serviceId).then((res) => {
				service.value.documents = res.documents.map((d) => d.id);
				service.value.steps = res.steps
					.sort((a, b) => a.pivot.position - b.pivot.position)
					.map((s) => {
						return {
							id: s.id,
							label: s.label,
							position: s.pivot.position,
							duration: s.pivot.duration,
						};
					});
				service.value.image = null;
			});
		}
		// crudStore.loadCreateData().then((res) => {
		// 	types.value = res.types;
		// 	requiredDocumentTypes.value = res.documents;
		// 	organizations.value = res.organizations;
		// 	services.value = res.services;
		// 	vehicleCategories.value = res.vehicle_categories;
		// 	steps.value = res.steps;

		// });
		//serviceStore.chargeData()
	});
</script>

<template>
	<div class="form-fieldset">
		<div class="fieldset-heading">
			<h4>Les Caractéristiques du Véhicule</h4>
		</div>
		<!-- <div v-for="vehicleCharacteristicCategorie in vehicleCaracteristicCategories"
			:key="vehicleCharacteristicCategorie.id">
			<p class="has-text-weight-semibold">{{ vehicleCharacteristicCategorie.label }}</p>
			<span v-for="vehicle_characteristic in vehicleCharacteristicCategorie.vehicle_characteristics"
				:key="vehicle_characteristic.id" class="m-4">
				<input type="checkbox" v-model="vehicle_characteristic.checked" class="mr-1" />
				<label for="">{{ vehicle_characteristic.value }}</label>

				<br v-if="vehicle_characteristic.checked" />
				<input type="number" v-if="vehicle_characteristic.checked" v-model="vehicle_characteristic.price" />
				<br v-if="vehicle_characteristic.checked" />
			</span>
		</div> -->

		<div class="column is-12">
			<VAccordion :items="vehicleCaracteristicCategories">
				<template #accordion-item-summary="{ item }">{{ item.label }}</template>
				<template #accordion-item-content="{ item }">
					<VAccordion :items="item.vehicle_characteristics" v-if="item.vehicle_characteristics.length != 0">
						<template #accordion-item-summary="item">
							{{ item.item.value }}
						</template>
						<template #accordion-item-content="item">
							<VField>
								<VLabel>Price</VLabel>
								<VControl fullwidth>
									<VInput name="price" v-model:model-value="item.item.price" />
								</VControl>
							</VField>
						</template>
					</VAccordion>
				</template>
			</VAccordion>
		</div>
	</div>
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
