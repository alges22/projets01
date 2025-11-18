<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner le type d’enregistrement</h2>
	</div>

	<VehicleTypeStep v-if="stepper.isCurrent('register_type')" @next="stepper.goTo('vehicle_info')" />

	<template v-else-if="stepper.isCurrent('vehicle_info')">
		<VehicleAlertedInfo :vehicle-info="vehicleInfo">
			<template #buttons>
				<div class="intro-y col-span-12">
					<div class="text-right mt-4">
						<button
							class="btn btn-outline-primary w-36 mr-4 border-2"
							type="reset"
							@click="stepper.goTo('register_type')"
						>
							Annuler
						</button>
						<button class="btn btn-primary w-36" type="button" @click="stepper.goTo('form')">
							Suivant
						</button>
					</div>
				</div>
			</template>
			<template #alerted-buttons>
				<div class="intro-y col-span-12">
					<div class="text-right mt-4">
						<button class="btn btn-dark w-36 mr-4 border-2" type="reset" @click="$router.go(-1)">
							Retour
						</button>
					</div>
				</div>
			</template>
		</VehicleAlertedInfo>
	</template>

	<PassageFormStep
		v-if="stepper.isCurrent('form')"
		@prev="stepper.goTo('vehicle_info')"
		@next="successModalIsOpen = true"
	/>

	<Modal :show="successModalIsOpen" backdrop="static" @hidden="successModalIsOpen = false">
		<ModalBody class="p-0">
			<div class="p-5 text-center font-bold">
				<svg
					class="lucide lucide-check-circle w-16 h-16 text-success mx-auto mt-3"
					fill="none"
					height="24"
					stroke="currentColor"
					stroke-linecap="round"
					stroke-linejoin="round"
					stroke-width="2"
					viewBox="0 0 24 24"
					width="24"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
					<polyline points="22 4 12 14.01 9 11.01"></polyline>
				</svg>
				<div class="text-2xl mt-5">Le véhicule a été enregistré avec succès !</div>
			</div>
			<div class="px-5 pb-8 text-center mt-4">
				<button class="btn btn-primary w-full mx-2" type="button" @click="closeModal">
					Ok, j'ai compris !
				</button>
			</div>
		</ModalBody>
	</Modal>
</template>

<script setup>
	import { useStepper } from "@vueuse/core";
	import { storeToRefs } from "pinia";
	import VehicleTypeStep from "@/views/Police/Passage/Steps/VehicleTypeStep.vue";
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import VehicleAlertedInfo from "@/views/Police/Verification/VehicleAlertedInfo.vue";
	import PassageFormStep from "@/views/Police/Passage/Steps/PassageFormStep.vue";
	import { Modal, ModalBody } from "@/global-components/modal/index.js";
	import { onUnmounted, ref } from "vue";
	import { onBeforeRouteLeave, useRouter } from "vue-router";

	const router = useRouter();
	const passageStore = useVehiclePassageStore();
	const { vehicleInfo } = storeToRefs(passageStore);

	const successModalIsOpen = ref(false);

	const stepper = useStepper({
		register_type: {
			title: "Type d'enregistrement",
		},
		vehicle_info: {
			title: "Informations sur le véhicule",
		},
		form: {
			title: "Formulaire",
		},
	});

	const closeModal = () => {
		successModalIsOpen.value = false;
		router.go(-1);
	};

	onUnmounted(() => {
		stepper.goBackTo("register_type");
		successModalIsOpen.value = false;
	});

	onBeforeRouteLeave(() => {
		stepper.goBackTo("register_type");
		successModalIsOpen.value = false;
	});
</script>
