<template>
	<form class="grid grid-cols-12 gap-6 mt-5" @submit.prevent="modalIsOpen = true">
		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="vehicle-provenance-1"
					v-model="passage.vehicle_provenance"
					:checked="passage.vehicle_provenance === 'local'"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="vehicle_provenance"
					type="radio"
					value="local"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="vehicle-provenance-1"
				>
					<div class="flex flex-col lg:flex-row items-center p-5">
						<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
							<img
								alt="Cliquez pour choisir la demande d'immatriculation"
								class=""
								src="../../../../assets/images/parc/car_green.png"
							/>
							<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
								<i class="fa-light fa-check"></i>
							</span>
						</div>
						<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
							<h2 class="font-semibold">Voiture local</h2>
							<div class="text-slate-500 text-xs mt-0.5">Regroupe les voitures</div>
						</div>
					</div>
				</label>
			</div>
		</div>

		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="vehicle-provenance-2"
					v-model="passage.vehicle_provenance"
					:checked="passage.vehicle_provenance === 'external'"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="vehicle_provenance"
					type="radio"
					value="external"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="vehicle-provenance-2"
				>
					<div class="flex flex-col lg:flex-row items-center p-5">
						<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F9933933] rounded-full p-2">
							<img
								alt="Cliquez pour choisir la demande d'immatriculation"
								class=""
								src="../../../../assets/images/parc/car_red.png"
							/>
							<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
								<i class="fa-light fa-check"></i>
							</span>
						</div>
						<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
							<h2 class="font-semibold">Voiture étranger</h2>
							<div class="text-slate-500 text-xs mt-0.5">Regroupe les voitures</div>
						</div>
					</div>
				</label>
			</div>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<BasicButton class="btn-outline-primary w-36 mr-4 border-2" type="reset" @click="$router.go(-1)">
					Annuler
				</BasicButton>
				<BasicButton
					:loading="formLoading || loading"
					class="btn-primary w-36"
					type="submit"
					:disabled="passage.vehicle_provenance !== 'local'"
				>
					Suivant
				</BasicButton>
			</div>
		</div>
	</form>

	<Modal :show="modalIsOpen" @hidden="modalIsOpen = false">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div>
					<TextInputGroup
						v-model="passage.immatriculation_number"
						name="immatriculation_number"
						add-class="w-full"
						label="Entrer le numéro d'immatriculation"
						:errors="errors.immatriculation_number"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<BasicButton
					:loading="formLoading"
					class="btn-primary w-full"
					type="button"
					:disabled="passage.vehicle_provenance !== 'local' && !passage.immatriculation_number"
					@click="fetchVehicle"
				>
					Suivant
				</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>
<script setup>
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { onMounted, onUnmounted, ref } from "vue";
	import { onBeforeRouteLeave } from "vue-router";

	const emit = defineEmits(["next", "prev"]);

	const passageStore = useVehiclePassageStore();
	const { passage, formLoading, loading, errors } = storeToRefs(passageStore);

	const modalIsOpen = ref(false);

	const fetchVehicle = async () => {
		await passageStore.fetchVehicle(passage.value.immatriculation_number).then((res) => {
			modalIsOpen.value = false;
		});
		emit("next");
	};

	onMounted(() => {
		if (passage.value && passage.value.vehicle_provenance === "local") {
			modalIsOpen.value = true;
		}
		loading.value = false;
	});

	onUnmounted(() => {
		modalIsOpen.value = false;
	});

	onBeforeRouteLeave(() => {
		modalIsOpen.value = false;
	});
</script>
