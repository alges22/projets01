<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner le type d’enregistrement</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="custom-checkbox-1"
					v-model="serviceType"
					checked
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="custom-checkbox"
					type="radio"
					value="1"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="custom-checkbox-1"
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
					id="custom-checkbox-2"
					v-model="serviceType"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="custom-checkbox"
					type="radio"
					value="2"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="custom-checkbox-2"
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
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
					Annuler
				</button>
				<button class="btn btn-primary w-36" type="button" @click="goNext">Suivant</button>
			</div>
		</div>

		<Modal :show="modalIsOpen" @hidden="modalIsOpen = false">
			<ModalBody>
				<div class="flex flex-col justify-between mx-4">
					<div>
						<label class="form-label" for="crud-form-1">Entrer le numéro d'immatriculation</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="00XY"
						/>
					</div>
				</div>
			</ModalBody>
			<ModalFooter class="">
				<div class="flex mb-2">
					<button class="btn btn-primary w-full" type="button" @click="closeModal">Vérifier</button>
				</div>
			</ModalFooter>
		</Modal>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { useRouter } from "vue-router";

	const router = useRouter();
	const serviceType = ref("1");
	const modalIsOpen = ref(false);

	const goNext = () => {
		if (serviceType.value === "1") {
			modalIsOpen.value = true;
		} else {
			router.push("/police/dashboard/register/other/OwnerInfo");
		}
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		router.push("/police/dashboard/register/local/VehicleInfo");
	};
</script>
