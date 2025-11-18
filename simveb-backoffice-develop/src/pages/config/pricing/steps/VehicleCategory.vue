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
	const { vehicleCategories, vehicleCategoryModelType } = storeToRefs(serviceStore);
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
		// serviceStore.chargeData()
	});
</script>

<template>
	<div class="form-fieldset">
		<div class="fieldset-heading">
			<h4>Les catégories de véhicule</h4>
		</div>

		<div>
			<VAccordion :items="vehicleCategories" v-if="vehicleCategories.length != 0">
				<template #accordion-item-summary="item">
					{{ item.item.label }}
				</template>
				<template #accordion-item-content="item">
					<div class="column is-12">
						<VField>
							<VLabel>Price</VLabel>
							<VControl fullwidth>
								<VInput name="price" v-model="item.item.price" />
							</VControl>
						</VField>
					</div>
				</template>
			</VAccordion>
		</div>

		<!-- <div v-if="vehicleCategories.length != 0">
			<span v-for="vehicleCategorie in vehicleCategories" :key="vehicleCategorie.id" class="m-4">
				<input type="checkbox" v-model="vehicleCategorie.checked" class="mr-1" />
				<label for="">{{ vehicleCategorie.label }}</label>
				<br v-if="vehicleCategorie.checked" />
				<input type="number" v-if="vehicleCategorie.checked" v-model="vehicleCategorie.price" />
				<br v-if="vehicleCategorie.checked" />
			</span>
		</div> -->
	</div>
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
