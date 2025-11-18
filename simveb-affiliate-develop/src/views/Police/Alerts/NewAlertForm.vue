<template>
	<Modal :show="open" size="modal-lg" @hidden="closeModal">
		<ModalHeader class="pt-6">
			<h2 class="font-bold text-base mr-auto">Créer une alerte</h2>
			<button class="absolute right-0 top-0 mt-6 mr-3" @click="closeModal">
				<i class="fa-solid fa-xmark-circle w-8 h-4 font-bold fa-xl" />
			</button>
		</ModalHeader>
		<ModalBody class="grid grid-cols-12 gap-4 gap-y-3">
			<div
				class="col-span-12 relative before:hidden before:lg:block before:absolute before:w-[40%] before:h-[3px] before:top-0 before:bottom-0 before:mt-4 before:bg-slate-100 before:dark:bg-darkmode-400 flex flex-col lg:flex-row justify-center px-5 sm:px-20"
			>
				<div
					v-for="(step, id, i) in stepper.steps.value"
					:key="id"
					class="intro-x lg:text-center flex items-center lg:block flex-1 z-10"
				>
					<button
						:disabled="stepper.isBefore(id)"
						class="w-10 h-10 rounded-full btn"
						:class="{
							'btn-primary': stepper.isCurrent(id),
							'btn-success': stepper.isAfter(id),
							'btn text-slate-500 bg-slate-100 dark:bg-darkmode-400 dark:border-darkmode-400':
								stepper.isBefore(id),
						}"
						@click="stepper.goTo(id)"
					>
						<template v-if="stepper.isAfter(id)">
							<i class="text-white fa-solid fa-check" />
						</template>
						<template v-else>
							{{ i + 1 }}
						</template>
					</button>
				</div>
			</div>

			<template v-if="stepper.isCurrent('vehicle-info')">
				<div v-if="immatriculationShown" class="intro-y col-span-12 mt-4">
					<TextInputGroup
						v-model="alert.immatriculation_number"
						name="immatriculation_number"
						class="w-full"
						label="Entrer le numéro d'immatriculation"
						:errors="errors.immatriculation_number"
					/>
				</div>
				<div class="intro-y flex col-span-12 items-center mt-4">
					<h4 class="text-base font-semibold mr-auto">Sélectionner le ou les motif(s)</h4>
				</div>

				<div class="col-span-12 grid grid-cols-12 gap-6 mt-5">
					<div
						v-for="(alertType, index) in formData.alert_types"
						:key="index"
						class="intro-y col-span-12 md:col-span-6"
					>
						<div class="relative group">
							<input
								:id="'alert_type_' + index"
								class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
								name="alert_type"
								type="checkbox"
								:value="alertType.id"
								:checked="alert.alert_types?.includes(alertType.id)"
								@change="
									(e) =>
										e.target.checked
											? alert.alert_types.push(alertType.id)
											: alert.alert_types.splice(alert.alert_types.indexOf(alertType.id), 1)
								"
							/>
							<label
								class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
								:for="'alert_type_' + index"
							>
								<span class="flex flex-col lg:flex-row items-center p-2">
									<span class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
										<img :alt="alertType.description" class="" :src="alertType.image" />
										<span
											class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
										>
											<i class="fa-light fa-check"></i>
										</span>
									</span>
									<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
										<h2 class="font-semibold">{{ alertType.name }}</h2>
									</div>
								</span>
							</label>
						</div>
					</div>
				</div>

				<div class="intro-y col-span-12 mt-4">
					<TextAreaInputGroup
						v-model="alert.comment"
						name="comment"
						label="Indication spéciale"
						:rows="4"
						:errors="errors.comment"
					/>
				</div>
				<div class="intro-y col-span-6 mt-4">
					<TextInputGroup
						v-model="alert.driver_lastname"
						name="driver_lastname"
						required
						label="Nom du conducteur"
						:errors="errors.driver_lastname"
					/>
				</div>
				<div class="intro-y col-span-6 mt-4">
					<TextInputGroup
						v-model="alert.driver_firstname"
						name="driver_firstname"
						required
						label="Prénom du conducteur"
						:errors="errors.driver_firstname"
					/>
				</div>
				<div class="intro-y col-span-12 mt-4">
					<div class="flex mb-4">
						<button
							:disabled="!stepper.at(0).isValid()"
							class="btn btn-danger w-full"
							type="button"
							@click="stepper.goTo('validation')"
						>
							Signaler la voiture
						</button>
					</div>
				</div>
			</template>
			<template v-else-if="stepper.isCurrent('validation')">
				<div class="col-span-12 mt-8">
					<Alert class="alert-outline-danger border-2 bg-dark-50 flex items-center mb-2 font-bold">
						<i class="fa-solid text-xl text-danger fa-exclamation-triangle w-6 h-6 mr-4" />
						<span class="text-dark">
							Assurez-vous que le informations concernant le véhicule sont exactes.
						</span>
					</Alert>
				</div>
				<div class="col-span-12">
					<p class="text-base mb-2 font-bold">Immatricule :</p>
					<ul class="px-8">
						<li class="list-disc text-base">{{ alert.immatriculation_number }}</li>
					</ul>

					<p class="text-base mb-2 mt-4 font-bold">Motif(s) :</p>
					<ul class="px-8">
						<li v-for="(alertType, index) in alert.alert_types" :key="index" class="list-disc text-base">
							{{ formData.alert_types.find((type) => type.id === alertType).name }}
						</li>
					</ul>
					<p class="text-base mb-2 mt-4 font-bold">Indication spéciale :</p>
					<p class="text-base mb-2">{{ alert.comment }}</p>
					<p class="text-base mb-2 mt-4 font-bold">Nom du conducteur :</p>
					<p class="text-base mb-2">{{ alert.driver_lastname }}</p>
					<p class="text-base mb-2 mt-4 font-bold">Prénom du conducteur :</p>
					<p class="text-base mb-2">{{ alert.driver_firstname }}</p>
				</div>
				<div class="col-span-12 mt-4">
					<div class="flex mb-4">
						<BasicButton
							:loading="formLoading || loading"
							class="btn-danger w-full"
							type="button"
							@click="makeAlert"
						>
							Confirmer
						</BasicButton>
					</div>
				</div>
			</template>
		</ModalBody>
	</Modal>

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
				<div class="text-2xl mt-5">Le véhicule a été signalé avec succès</div>
			</div>
			<div class="px-5 pb-8 text-center mt-4">
				<button class="btn btn-danger w-full mx-2" type="button" @click="successModalIsOpen = false">
					Ok j'ai compris !
				</button>
			</div>
		</ModalBody>
	</Modal>
</template>
<script setup>
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import Alert from "@/global-components/alert/index.js";
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { onMounted, ref, watch } from "vue";
	import { useStepper } from "@vueuse/core";
	import BasicButton from "@/components/BasicButton.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import TextAreaInputGroup from "@/components/Form/TextAreaInputGroup.vue";

	const props = defineProps({
		open: {
			type: Boolean,
			default: false,
		},
		immatriculationShown: {
			type: Boolean,
			default: true,
		},
	});
	const emit = defineEmits(["submit", "close"]);

	const alertStore = useCrudStore();
	const { row: alert, loading, url, errors, formData, formLoading, formDataLoaded } = storeToRefs(alertStore);

	const successModalIsOpen = ref(false);

	const stepper = useStepper({
		"vehicle-info": {
			title: "Informations du véhicule",
			isValid: () =>
				alert.value.alert_types?.length > 0 && alert.value.driver_lastname && alert.value.driver_firstname,
		},
		validation: {
			title: "Validation",
			isValid: () => true,
		},
	});

	const makeAlert = async () => {
		await alertStore
			.createRow(alert.value)
			.then(() => {
				emit("submit");
				successModalIsOpen.value = true;
				alert.value = {};
				alertStore.fetchRows({});
			})
			.catch(() => {
				stepper.goToPrevious();
			});
	};

	watch(
		() => props.open,
		(value) => {
			if (value && !formDataLoaded.value) {
				url.value = "vehicle-alerts";
				alertStore.loadCreateData().then(() => {
					alert.value.alert_types = [];
				});
			}
		}
	);

	const closeModal = () => {
		emit("close");
		alert.value = {};
		stepper.goBackTo("vehicle-info");
	};

	onMounted(() => {
		loading.value = false;
	});
</script>
