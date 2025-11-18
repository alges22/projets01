<template>
	<LoaderSpinner v-if="loading" />

	<MotifForm v-else-if="stepper.isCurrent('motif_form')" @next="stepper.goTo('file_upload')" @prev="$router.back()" />

	<FileForm v-else-if="stepper.isCurrent('file_upload')" @prev="stepper.goTo('motif_form')" @next="submit" />
</template>

<script setup>
	import { useStepper } from "@vueuse/core";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onBeforeUnmount, onMounted } from "vue";
	import { onBeforeRouteLeave, useRouter } from "vue-router";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";
	import MotifForm from "@/views/Demands/Title/Deposit/MotifForm.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import Swal from "sweetalert2";

	const demandStore = useDemandStore();
	const { demand, loading, update } = storeToRefs(demandStore);
	const router = useRouter();

	const stepper = useStepper({
		motif_form: {
			title: "Motif de dépôt de titre",
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

	// Hook

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
				service_id: res.demand.serviceId,
			};
		});
		loading.value = false;
	});
</script>

<style scoped></style>
