<template>
	<OwnerInfo v-if="stepper.isCurrent('owner_info')" title="Propriétaire" @next="stepper.goTo('car_choice')" />

	<CarChoice
		v-else-if="stepper.isCurrent('car_choice')"
		@next="stepper.goTo('personnalisation')"
		@prev="stepper.goTo('owner_info')"
	/>

	<PersonnalisationForm
		v-else-if="stepper.isCurrent('personnalisation')"
		@next="stepper.goTo('file_upload')"
		@prev="stepper.goTo('car_choice')"
	/>

	<FileForm
		v-else-if="stepper.isCurrent('file_upload')"
		@prev="stepper.goTo('personnalisation')"
		@next="stepper.goTo('recap')"
	/>

	<Recap v-else-if="stepper.isCurrent('recap')" service="Transformation" @prev="stepper.goTo('file_upload')">
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
	import CarChoice from "@/views/Demands/Sale/CarChoice.vue";
	import OwnerInfo from "@/views/Demands/Sale/OwnerInfo.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";
	import PersonnalisationForm from "@/views/Demands/Transformation/PersonnalisationForm.vue";

	const demandStore = useDemandStore();
	const { demand, loading, owner_info, formData } = storeToRefs(demandStore);

	const stepper = useStepper({
		owner_info: {
			title: "Informations sur le propriétaire",
		},
		car_choice: {
			title: "Choix du véhicule",
		},
		personnalisation: {
			title: "Personnalisation",
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
			await demandStore.loadForm(props.serviceId);
		}
		loading.value = false;
	});
</script>

<style scoped></style>
