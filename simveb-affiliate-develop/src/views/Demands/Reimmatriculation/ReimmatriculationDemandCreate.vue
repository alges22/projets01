<template>
	<OwnerInfo
		v-if="stepper.isCurrent('owner_info')"
		title="Propriétaire"
		@prev="$router.back()"
		@next="stepper.goToNext()"
	/>

	<VehicleForm v-if="stepper.isCurrent('vehicle_form')" @next="stepper.goToNext()" @prev="stepper.goToPrevious()" />

	<VehicleInfo
		v-else-if="stepper.isCurrent('vehicle_info')"
		@next="stepper.goTo('service_choice')"
		@prev="stepper.goTo('vehicle_form')"
	/>

	<ServiceChoiceForm
		v-else-if="stepper.isCurrent('service_choice')"
		@next="stepper.goTo(bindServiceStep())"
		@prev="stepper.goTo('vehicle_info')"
	/>

	<NumberChoiceForm
		v-else-if="stepper.isCurrent('number_choice')"
		@prev="stepper.goTo('service_choice')"
		@next="
			demand.serviceCode === 'IMMATRICULATION_PRESTIGE_NUMBER_LABEL'
				? stepper.goTo('label_choice')
				: stepper.goTo('plate_caracteristics')
		"
	/>

	<LabelChoiceForm
		v-else-if="stepper.isCurrent('label_choice')"
		@prev="
			demand.serviceCode === 'IMMATRICULATION_PRESTIGE_NUMBER_LABEL'
				? stepper.goTo('number_choice')
				: stepper.goTo('service_choice')
		"
		@next="stepper.goTo('plate_caracteristics')"
	/>

	<PlateCaracteristicForm
		v-else-if="stepper.isCurrent('plate_caracteristics')"
		@prev="stepper.goTo('service_choice')"
		@next="stepper.goTo('file_upload')"
	/>

	<FileForm
		v-else-if="stepper.isCurrent('file_upload')"
		@prev="stepper.goTo('plate_caracteristics')"
		@next="stepper.goTo('recap')"
	/>

	<Recap
		v-else-if="stepper.isCurrent('recap')"
		service="Demande de Ré-immatriculation"
		@prev="stepper.goTo('file_upload')"
	>
		<VehicleInfoCard :vehicle-info="vehicle_info" :loading="loading" />

		<OwnerInfoCard :owner-info="owner_info" />
	</Recap>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted, ref } from "vue";
	import { useStepper } from "@vueuse/core";
	import VehicleForm from "@/views/Demands/Reimmatriculation/VehicleForm.vue";
	import VehicleInfo from "@/views/Demands/Steps/VehicleInfo.vue";
	import PlateCaracteristicForm from "@/views/Demands/Steps/PlateCaracteristicForm.vue";
	import Recap from "@/views/Demands/Steps/Recap.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import LabelChoiceForm from "@/views/Demands/Steps/LabelChoiceForm.vue";
	import NumberChoiceForm from "@/views/Demands/Steps/NumberChoiceForm.vue";
	import OwnerInfo from "@/views/Demands/Sale/OwnerInfo.vue";
	import ServiceChoiceForm from "@/views/Demands/Reimmatriculation/ServiceChoiceForm.vue";

	const demandStore = useDemandStore();
	const { demand, loading, formData, vehicle_info, owner_info } = storeToRefs(demandStore);

	const stepper = useStepper({
		owner_info: {
			title: "Informations sur le propriétaire",
		},
		vehicle_form: {
			title: "Informations du véhicule",
			isValid: true,
		},
		service_choice: {
			title: "Choix du service",
			isValid: true,
		},
		vehicle_info: {
			title: "Informations sur le véhicule",
		},
		number_choice: {
			title: "Choix du numéro",
		},
		label_choice: {
			title: "Choix du label",
		},
		plate_caracteristics: {
			title: "Caractéristiques de la plaque",
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

	const modalIsOpen = ref(false);

	const bindServiceStep = () => {
		switch (demand.value.serviceCode) {
			case "IMMATRICULATION_STANDARD": {
				return "plate_caracteristics";
			}
			case "IMMATRICULATION_PRESTIGE_NUMBER_LABEL" || "IMMATRICULATION_PRESTIGE_NUMBER": {
				return "number_choice";
			}
			case "IMMATRICULATION_PRESTIGE_LABEL": {
				return "label_choice";
			}
		}
	};

	onBeforeMount(() => {
		demand.value = {
			service_id: props.serviceId,
			is_car: 1,
		};
	});

	onMounted(async () => {
		if (props.demandId) {
			await demandStore.fetchDemand(props.demandId);
		}

		if (!demand.value || !demand.value.vin) {
			modalIsOpen.value = true;
		}

		if (!formData.value) {
			demandStore.loadForm(props.serviceId);
		}
		loading.value = false;
	});
</script>

<style scoped></style>
