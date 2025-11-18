<template>
	<OwnerInfo
		v-if="stepper.isCurrent('owner_info')"
		title="Acheteur"
		@next="stepper.goTo('vehicle_info')"
		@prev="$router.back()"
	/>

	<VehicleInfo
		v-else-if="stepper.isCurrent('vehicle_info')"
		@next="stepper.goTo('file_upload')"
		@prev="stepper.goTo('owner_info')"
	/>

	<FileForm
		v-else-if="stepper.isCurrent('file_upload')"
		@prev="stepper.goTo('vehicle_info')"
		@next="stepper.goTo('recap')"
	/>

	<Recap v-else-if="stepper.isCurrent('recap')" service="Reprise de titre" @prev="stepper.goTo('file_upload')">
		<VehicleInfoCard :vehicle-info="vehicle_info" />

		<OwnerInfoCard :owner-info="owner_info" />
	</Recap>
</template>

<script setup>
	import { useStepper } from "@vueuse/core";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted, onUnmounted } from "vue";
	import Recap from "@/views/Demands/Steps/Recap.vue";
	import { onBeforeRouteLeave } from "vue-router";
	import OwnerInfo from "@/views/Demands/Sale/OwnerInfo.vue";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import VehicleInfo from "@/views/Demands/Title/Recovery/VehicleInfo.vue";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";

	const demandStore = useDemandStore();
	const { demand, loading, vehicle_info, owner_info, formData } = storeToRefs(demandStore);

	const stepper = useStepper({
		owner_info: {
			title: "Informations sur le propriétaire",
		},
		vehicle_info: {
			title: "Informations du véhicule",
		},
		file_upload: {
			title: "Pièces justificatives",
		},
		recap: {
			title: "Récapitulatif",
		},
	});

	const props = defineProps({
		demandId: {
			type: String,
			default: null,
		},
		serviceId: {
			type: String,
			default: null,
			required: true,
		},
	});

	// Hook

	onBeforeMount(() => {
		demand.value = {
			service_id: props.serviceId,
		};
	});

	onUnmounted(() => {
		demandStore.clear();
	});

	onBeforeRouteLeave(() => {
		demandStore.clear();
	});

	onMounted(async () => {
		if (props.demandId) {
			await demandStore.fetchDemand(props.demandId);
			stepper.goTo(demand.value.step);
		}

		if (!formData.value) {
			demandStore.loadForm(props.serviceId);
		}
		loading.value = false;
	});
</script>

<style scoped></style>
