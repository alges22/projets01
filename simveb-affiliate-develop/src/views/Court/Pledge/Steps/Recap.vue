<script setup>
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import { usePledgeStore } from "@/stores/pledge.js";
	import { storeToRefs } from "pinia";
	import OtpModalForm from "@/views/OtpModalForm.vue";
	import { ref } from "vue";

	const pledgeStore = usePledgeStore();
	const { vehicle_info, owner_info } = storeToRefs(pledgeStore);

	const emit = defineEmits(["next", "prev"]);

	const otpModalOpen = ref(false);

	const goNext = (authorization_id) => {
		otpModalOpen.value = false;

		emit("next");
	};
</script>

<template>
	<div class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
		<VehicleInfoCard :vehicle-info="vehicle_info" />

		<OwnerInfoCard :owner-info="owner_info" />
	</div>

	<div class="text-right mt-5">
		<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">Annuler</button>
		<button class="btn btn-primary w-36" type="button" @click="otpModalOpen = !otpModalOpen">Confirmer</button>
	</div>

	<OtpModalForm
		:npi="owner_info?.npi"
		:is-company="false"
		:open="otpModalOpen"
		@submit="(authorization_id) => goNext(authorization_id)"
		@close="otpModalOpen = false"
	/>
</template>

<style lang="scss" scoped></style>
