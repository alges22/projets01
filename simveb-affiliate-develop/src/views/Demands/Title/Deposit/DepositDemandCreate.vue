<template>
	<OwnerInfo v-if="stepper.isCurrent('owner_info')" @next="stepper.goTo('car_choice')" @prev="$router.back()" />

	<CarChoice
		v-else-if="stepper.isCurrent('car_choice')"
		@next="stepper.goTo('motif_form')"
		@prev="stepper.goTo('owner_info')"
	/>

	<MotifForm
		v-else-if="stepper.isCurrent('motif_form')"
		@next="stepper.goTo('file_upload')"
		@prev="stepper.goTo('car_choice')"
	/>

	<FileForm
		v-else-if="stepper.isCurrent('file_upload')"
		@prev="stepper.goTo('motif_form')"
		@next="stepper.goTo('recap')"
	/>

	<Recap v-else-if="stepper.isCurrent('recap')" service="Dépôt de titre" @prev="stepper.goTo('file_upload')">
		<VehicleInfoCard :vehicle-info="vehicle_info" />
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
	import FileForm from "@/views/Demands/Steps/FileForm.vue";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import CarChoice from "@/views/Demands/Sale/CarChoice.vue";
	import MotifForm from "@/views/Demands/Title/Deposit/MotifForm.vue";

	const demandStore = useDemandStore();
	const { demand, loading, formData, vehicle_info } = storeToRefs(demandStore);

	const stepper = useStepper({
		owner_info: {
			title: "Informations sur le propriétaire",
		},
		car_choice: {
			title: "Choix du véhicule",
		},
		motif_form: {
			title: "Motif de dépôt de titre",
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
