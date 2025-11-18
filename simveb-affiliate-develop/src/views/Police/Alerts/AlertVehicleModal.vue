<template>
	<button class="btn btn-outline-danger mr-4 border-2" type="button" @click="alertTypeModalIsOpen = true">
		Signaler la voiture
	</button>

	<Modal :show="alertTypeModalIsOpen" size="modal-xl" @hidden="alertTypeModalIsOpen = false">
		<ModalHeader class="pt-6">
			<h2 class="font-bold text-base mr-auto">Créer une alerte</h2>
			<button class="absolute right-0 top-0 mt-6 mr-3" @click="closeAlertTypeModal">
				<i class="fa-solid fa-x w-8 h-4 font-bold" />
			</button>
		</ModalHeader>
		<ModalBody v-if="step === '1'">
			<div class="m-5">
				<span class="bg-blue-600 p-5 rounded-full text-white">1</span>
				----------------
				<span class="bg-slate-600 p-5 rounded-full text-white">2</span>
			</div>

			<div class="flex flex-col justify-between mx-4">
				<div>
					<span class="form-label" for="crud-form-1">Entrer le numéro d'immatriculation</span>
					<input
						id="crud-form-1"
						:value="registrationNumber"
						class="form-control w-full"
						placeholder="Input text"
						type="text"
						disabled
					/>
				</div>
			</div>

			<div class="flex flex-col justify-between mx-4">
				<div>
					<span class="form-label" for="crud-form-1">Sélectionner le ou les motif(s)</span>
				</div>
			</div>

			<div class="grid grid-cols-12 gap-6 mt-5">
				<!-- begin item -->
				<div v-for="alertType in alertTypes" :key="alertType.code" class="intro-y col-span-12 md:col-span-6">
					<div class="relative group">
						<input
							:id="alertType.code"
							:checked="selectedAlertTypesCodes.includes(alertType.code)"
							class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
							:name="alertType.code"
							type="radio"
							:value="alertType.code"
						/>
						<span
							class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
							for="custom-checkbox-1"
							@click="handleAlertTypeClick(alertType.code)"
						>
							<div class="flex flex-col lg:flex-row items-center p-5">
								<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
									<img
										alt="Cliquez pour choisir la demande d'immatriculation"
										class=""
										src="@/assets/images/police/GAGE.png"
									/>
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</div>
								<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
									<h2 class="font-semibold">{{ alertType.name }}</h2>
									<div class="text-slate-500 text-xs mt-0.5"></div>
								</div>
							</div>
						</span>
					</div>
				</div>
				<!-- end item -->
			</div>

			<div class="flex flex-col justify-between mx-4">
				<div>
					<span class="form-label" for="crud-form-1">Nom du conducteur</span>
					<input
						id="crud-form-1"
						v-model="driverLastName"
						class="form-control w-full"
						placeholder="Input text"
						type="text"
					/>
				</div>
			</div>

			<div class="flex flex-col justify-between mx-4">
				<div>
					<span class="form-label" for="crud-form-1">Prénom du conducteur</span>
					<input
						id="crud-form-1"
						v-model="driverFirstName"
						class="form-control w-full"
						placeholder="Input text"
						type="text"
					/>
				</div>
			</div>

			<div class="flex flex-col justify-between mx-4">
				<div class="intro-y col-span-12 mt-4">
					<label for="input-text-1" class="form-label"> Indication spéciale </label>
					<textarea
						id="input-text-1"
						v-model="comment"
						type="text"
						class="form-control"
						rows="4"
						placeholder="Description"
					>
					</textarea>
				</div>
			</div>
		</ModalBody>
		<ModalBody v-if="step === '2'">
			<div class="col-span-12 mt-8">
				<Alert class="alert-outline-danger bg-red-200 border-2 bg-dark-50 flex items-center mb-2 font-bold">
					<i class="fa-solid text-xl text-danger fa-exclamation-triangle w-6 h-6 mr-4" />
					<span class="text-dark">
						Assurez-vous que le informations concernant le véhicule sont exactes.
					</span>
				</Alert>
			</div>
			<div class="col-span-12">
				<p class="text-base mb-2 font-bold">Immatricule :</p>
				<ul class="px-8">
					<li class="list-disc text-base">{{ registrationNumber }}</li>
				</ul>

				<p class="text-base mb-2 mt-4 font-bold">Motif(s) :</p>
				<ul class="px-8">
					<li v-for="alertName in selectedAlertTypesNames" class="list-disc text-base">
						{{ alertName }}
					</li>
				</ul>
				<p class="text-base mb-2 mt-4 font-bold">Indication spéciale :</p>
				<p class="text-base mb-2">{{ comment }}</p>

				<p class="text-base mb-2 mt-4 font-bold">Nom du conducteur :</p>
				<p class="text-base mb-2">{{ driverLastName }}</p>

				<p class="text-base mb-2 mt-4 font-bold">Prénom du conducteur :</p>
				<p class="text-base mb-2">{{ driverFirstName }}</p>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button v-if="step === '1'" class="btn bg-red-600 text-white w-full" type="button" @click="step = '2'">
					Signaler la voiture
				</button>

				<button v-if="step === '2'" class="btn bg-red-600 text-white w-full" type="button" @click="makeAlert">
					Confirmer
				</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	const props = defineProps({
		// alertTypeModalIsOpen: {
		// 	type: Boolean,
		// 	default: false,
		// },
		registrationNumber: { type: String, default: "" },
	});
	const emit = defineEmits(["close-alert-modal"]);

	import { onMounted, ref } from "vue";
	import { useAlertStore } from "@/stores/alert";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import Alert from "@/global-components/alert/index.js";
	import { storeToRefs } from "pinia";
	import AlertNotification from "@/components/notification/alert.js";

	const alertStore = useAlertStore();
	const { alertTypes } = storeToRefs(alertStore);

	const selectedAlertTypesCodes = ref([]);
	const selectedAlertTypesNames = ref([]);
	const selectedAlertTypesIds = ref([]);
	const driverFirstName = ref("");
	const driverLastName = ref("");
	const comment = ref("");
	const step = ref("1");
	const alertTypeModalIsOpen = ref(false);

	const handleAlertTypeClick = (code) => {
		if (selectedAlertTypesCodes.value.includes(code)) {
			let index = selectedAlertTypesCodes.value.indexOf(code);
			if (index !== -1) {
				selectedAlertTypesCodes.value.splice(index, 1);
			}
		} else {
			selectedAlertTypesCodes.value.push(code);
		}

		selectedAlertTypesNames.value = alertTypes.value.filter((alertType) =>
			selectedAlertTypesCodes.value.includes(alertType.code)
		);
		selectedAlertTypesNames.value = selectedAlertTypesNames.value.map((type) => type.name);
	};

	const makeAlert = () => {
		selectedAlertTypesIds.value = alertTypes.value.filter((alertType) =>
			selectedAlertTypesCodes.value.includes(alertType.code)
		);
		selectedAlertTypesIds.value = selectedAlertTypesIds.value.map((type) => type.id);

		alertStore
			.makeAlert({
				immatriculation_number: props.registrationNumber,
				alert_types: selectedAlertTypesIds.value,
				driver_firstname: driverFirstName.value,
				driver_lastname: driverLastName.value,
				comment: comment.value,
			})
			.then((data) => {
				AlertNotification.success("Véhicule alerté!");
			});
		closeAlertTypeModal();
	};

	const closeAlertTypeModal = () => {
		alertTypeModalIsOpen.value = false;
		step.value = "1";
	};

	onMounted(() => {
		alertStore.fetchAlertTypes();
	});
</script>
