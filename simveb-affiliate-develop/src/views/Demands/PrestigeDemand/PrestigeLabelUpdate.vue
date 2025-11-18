<template>
	<LoaderSpinner v-if="loading" />

	<LabelChoiceForm
		v-else-if="stepper.isCurrent('label_choice')"
		@next="stepper.goTo('plate_caracteristics')"
		@prev="$router.back()"
	/>

	<PlateCaracteristicForm
		v-else-if="stepper.isCurrent('plate_caracteristics')"
		@prev="stepper.goTo('label_choice')"
		@next="stepper.goTo('file_upload')"
	/>

	<FileForm
		v-else-if="stepper.isCurrent('file_upload')"
		@prev="stepper.goTo('plate_caracteristics')"
		@next="submit"
	/>
</template>

<script setup>
	import { useStepper } from "@vueuse/core";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onBeforeUnmount, onMounted } from "vue";
	import PlateCaracteristicForm from "@/views/Demands/Steps/PlateCaracteristicForm.vue";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";
	import { onBeforeRouteLeave, useRouter } from "vue-router";
	import LabelChoiceForm from "@/views/Demands/Steps/LabelChoiceForm.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import Swal from "sweetalert2";

	const demandStore = useDemandStore();
	const { demand, loading, update } = storeToRefs(demandStore);
	const router = useRouter();

	const stepper = useStepper({
		label_choice: {
			title: "Choix du label",
		},
		plate_caracteristics: {
			title: "Caractéristiques de la plaque",
		},
		file_upload: {
			title: "Pièces justificatives",
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

	const submit = () => {
		Swal.fire({
			title: "Confirmation",
			text: "Vous êtes sur le point de modifier votre demande. Voulez-vous continuer ?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Continuez",
			cancelButtonText: "Non, Annulez",
		}).then((result) => {
			if (result.isConfirmed) {
				demandStore.updateDemand(demand.value.id, demand.value).then(() => {
					Swal.fire("Succès", "La modification a été prise en compte.", "success");
				});
			}
			router.back();
		});
	};

	// Hook

	onBeforeMount(() => {
		loading.value = true;
	});

	onBeforeUnmount(() => {
		demandStore.clear();
	});

	onBeforeRouteLeave(() => {
		demandStore.clear();
	});

	onMounted(async () => {
		update.value = true;
		await demandStore.editDemand(props.demandId).then((res) => {
			demand.value = {
				id: res.demand.id,
				front_plate_shape_id: res.demand.immatriculation.front_plate_shape_id,
				back_plate_shape_id: res.demand.immatriculation.back_plate_shape_id,
				plate_color_id: res.demand.immatriculation.plate_color_id,
				service_id: res.demand.serviceId,
				label: res.demand.model.label,
			};
		});
		loading.value = false;
	});
</script>

<style scoped></style>
