<template>
	<template v-if="demand.userType">
		<OwnerInfo
			v-if="stepper.isCurrent('owner_info')"
			@next="stepper.goTo('vehicle_verif')"
			@prev="$router.back()"
		/>

		<VehiculeInfoForm
			v-else-if="stepper.isCurrent('vehicle_verif')"
			@next="stepper.goTo('vehicle_info')"
			@prev="stepper.goTo('owner_info')"
		/>

		<VehicleInfo
			v-else-if="stepper.isCurrent('vehicle_info')"
			@next="stepper.goTo('plate_caracteristics')"
			@prev="stepper.goTo('vehicle_verif')"
		/>

		<PlateCaracteristicForm
			v-else-if="stepper.isCurrent('plate_caracteristics')"
			@prev="stepper.goTo('vehicle_info')"
			@next="stepper.goTo('file_upload')"
		/>

		<FileForm
			v-else-if="stepper.isCurrent('file_upload')"
			@prev="stepper.goTo('plate_caracteristics')"
			@next="stepper.goTo('recap')"
		/>

		<Recap
			v-else-if="stepper.isCurrent('recap')"
			service="Demande d'immatriculation standard"
			@prev="stepper.goTo('file_upload')"
		>
			<VehicleInfoCard :vehicle-info="vehicle_info" :loading="loading" />

			<OwnerInfoCard :owner-info="owner_info" />
		</Recap>
	</template>

	<Modal :show="userTypeModal" backdrop="static" @hidden="userTypeModal = false">
		<ModalBody>
			<div class="grid grid-cols-12 gap-6 mt-5">
				<div class="intro-y col-span-12">
					<div class="relative group">
						<input
							id="user-type-physique"
							v-model="userType"
							class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
							name="user-type"
							type="radio"
							value="physique"
						/>
						<label
							for="user-type-physique"
							class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						>
							<span class="flex flex-col lg:flex-row items-center p-5">
								<span class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#EBC81333] rounded-full p-2">
									<img
										alt="Cliquez pour choisir la demande d'immatriculation"
										class=""
										src="../../../assets/images/parc/user_sample.png"
									/>
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</span>
								<span class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
									<span class="h-2 font-semibold">Personne physique</span>
								</span>
							</span>
						</label>
					</div>
				</div>

				<div class="intro-y col-span-12">
					<div class="relative group">
						<input
							id="user-type-company"
							v-model="userType"
							class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
							name="user-type"
							type="radio"
							value="company"
						/>
						<label
							for="user-type-company"
							class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						>
							<span class="flex flex-col lg:flex-row items-center p-5">
								<span class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7505033] rounded-full p-2">
									<img
										alt="Cliquez pour choisir la demande d'immatriculation"
										class=""
										src="../../../assets/images/parc/building_sample.png"
									/>
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</span>
								<span class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
									<span class="h-2 font-semibold">Personne moral</span>
								</span>
							</span>
						</label>
					</div>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-outline-secondary w-full mr-4 border-2" type="reset" @click="$router.back()">
					Annuler
				</button>
				<button class="btn btn-primary w-full" type="button" @click="bindUserType">Suivant</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { useStepper } from "@vueuse/core";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onBeforeUnmount, onMounted, ref } from "vue";
	import OwnerInfo from "@/views/Demands/Steps/OwnerInfo.vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import VehiculeInfoForm from "@/views/Demands/Steps/VehiculeInfoForm.vue";
	import VehicleInfo from "@/views/Demands/Steps/VehicleInfo.vue";
	import PlateCaracteristicForm from "@/views/Demands/Steps/PlateCaracteristicForm.vue";
	import FileForm from "@/views/Demands/Steps/FileForm.vue";
	import Recap from "@/views/Demands/Steps/Recap.vue";
	import { onBeforeRouteLeave } from "vue-router";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";

	const demandStore = useDemandStore();
	const { demand, loading, formData, vehicle_info, owner_info } = storeToRefs(demandStore);
	const userTypeModal = ref(false);
	const userType = ref("physique");

	const stepper = useStepper({
		owner_info: {
			title: "Informations sur le propriétaire",
		},
		vehicle_verif: {
			title: "Vérification du véhicule",
		},
		vehicle_info: {
			title: "Informations sur le véhicule",
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

	const bindUserType = () => {
		userTypeModal.value = false;
		demand.value.userType = userType.value;
	};

	// Hook

	onBeforeMount(() => {
		demand.value = {
			service_id: props.serviceId,
			is_car: 1,
		};
	});

	onBeforeUnmount(() => {
		userTypeModal.value = false;
		demandStore.clear();
	});

	onBeforeRouteLeave(() => {
		userTypeModal.value = false;
		demandStore.clear();
	});

	onMounted(async () => {
		if (props.demandId) {
			await demandStore.fetchDemand(props.demandId);
			stepper.goTo(demand.value.step);
		}

		if (!demand.value || !demand.value.userType) {
			userTypeModal.value = true;
		}

		if (!formData.value) {
			demandStore.loadForm(props.serviceId);
		}
		loading.value = false;
	});
</script>

<style scoped></style>
