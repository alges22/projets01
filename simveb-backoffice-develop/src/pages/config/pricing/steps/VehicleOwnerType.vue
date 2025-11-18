<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useServiceStore } from "/@src/stores/modules/service";

	const props = defineProps({
		serviceId: {
			type: String,
			default: null,
		},
	});
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const stepStore = useStepStore();
	const serviceStore = useServiceStore();
	const { vehicleOwnerTypes } = storeToRefs(serviceStore);
	const update = ref(false);
	const { url, formLoading, errors, row: service, loading } = storeToRefs(crudStore);
	const { activeStep } = storeToRefs(stepStore);

	onMounted(() => {
		if (props.serviceId) {
			update.value = true;
		}
		serviceStore.chargeData();
	});

	const submit = () => {
		if (update.value) {
			crudStore.updateWithFile(service.value.id, service.value).then(() => {
				notyf.success("Le service a bien été modifié");
				router.push({ name: "services" });
			});
		} else {
			crudStore.createWithFile(service.value).then(() => {
				notyf.success("Le service a bien été crée");
				router.push({ name: "services" });
			});
		}
	};
</script>

<template>
	<div class="form-fieldset">
		<div class="fieldset-heading">
			<h4>Les types de propriétaires</h4>
		</div>
		<!-- <div v-if="vehicleOwnerTypes.length != 0">
			<span v-for="vehicleOwnerType in vehicleOwnerTypes" :key="vehicleOwnerType.id" class="m-4">
				<input type="checkbox" v-model="vehicleOwnerType.checked" class="mr-1" />
				<label for="">{{ vehicleOwnerType.label }}</label>
				<br v-if="vehicleOwnerType.checked" />
				<input type="number" v-if="vehicleOwnerType.checked" v-model="vehicleOwnerType.price" />
				<br v-if="vehicleOwnerType.checked" />
			</span>
		</div> -->
		<VAccordion :items="vehicleOwnerTypes" v-if="vehicleOwnerTypes.length != 0">
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
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
