<template>
	<form
		class="flex justify-between items-end dashboard-card !rounded-md mt-4 lg:mt-16"
		@submit.prevent="fetchVehicle"
	>
		<div class="w-full">
			<TextInputGroup
				v-model="immatriculationNumber"
				name="immatriculation_number"
				class="w-full"
				label="Entrer le numéro d'immatriculation"
				:errors="errors.immatriculation_number"
				add-class="w-full"
				required
			/>
		</div>
		<div class="ms-2 w-1/5">
			<BasicButton class="btn-primary w-full h-auto" type="submit" :loading="loading"> Vérifier </BasicButton>
		</div>
	</form>
	<LoaderSpinner v-if="loading" type="block" />
	<VehicleAlertedInfo v-else-if="vehicleInfo" :vehicle-info="vehicleInfo" />
</template>

<script setup>
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import { storeToRefs } from "pinia";
	import { onMounted, ref } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import VehicleAlertedInfo from "@/views/Police/Verification/VehicleAlertedInfo.vue";

	const passageStore = useVehiclePassageStore();
	const { immatriculationNumber, loading, errors } = storeToRefs(passageStore);
	const vehicleInfo = ref(null);

	const fetchVehicle = async () => {
		await passageStore.fetchVehicle(immatriculationNumber.value).then((res) => {
			vehicleInfo.value = res;
		});
	};

	onMounted(async () => {
		if (immatriculationNumber.value) {
			await fetchVehicle();
		}
	});
</script>
