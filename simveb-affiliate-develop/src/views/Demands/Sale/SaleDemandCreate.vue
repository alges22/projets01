<template>
	<OwnerInfo v-if="stepper.isCurrent('owner_info')" @next="stepper.goTo('car_choice')" @prev="$router.back()" />

	<CarChoice
		v-else-if="stepper.isCurrent('car_choice')"
		@next="stepper.goTo('buyer_info')"
		@prev="stepper.goTo('owner_info')"
	/>

	<BuyerInfo
		v-else-if="stepper.isCurrent('buyer_info')"
		@next="stepper.goTo('file_upload')"
		@prev="stepper.goTo('car_choice')"
	/>

	<FileForm
		v-else-if="stepper.isCurrent('file_upload')"
		@prev="stepper.goTo('buyer_info')"
		@next="stepper.goTo('recap')"
	/>

	<Recap v-else-if="stepper.isCurrent('recap')" service="Vente" @prev="stepper.goTo('file_upload')">
		<OwnerInfoCard :owner-info="buyer_info" title="Informations sur l'acheteur" />
		<OwnerInfoCard :owner-info="owner_info" />
	</Recap>
</template>

<script setup>
	import { useStepper } from "@vueuse/core";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted } from "vue";
	import Recap from "@/views/Demands/Steps/Recap.vue";
	import { onBeforeRouteLeave } from "vue-router";
	import CarChoice from "@/views/Demands/Sale/CarChoice.vue";
	import OwnerInfo from "@/views/Demands/Sale/OwnerInfo.vue";
	import BuyerInfo from "@/views/Demands/Sale/BuyerInfo.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";

	const demandStore = useDemandStore();
	const { demand, loading, buyer_info, owner_info, formData } = storeToRefs(demandStore);

	const stepper = useStepper({
		owner_info: {
			title: "Informations sur le propriétaire",
		},
		car_choice: {
			title: "Choix du véhicule",
		},
		buyer_info: {
			title: "Informations de l'acheteur",
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

	/*onUnmounted(() => {
		demandStore.clear();
	});*/

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
