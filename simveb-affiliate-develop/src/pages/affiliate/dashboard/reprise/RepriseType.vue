<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner le type de service</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="custom-checkbox-1"
					v-model="repriseType"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="custom-checkbox"
					type="radio"
					value="deposit"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="custom-checkbox-1"
				>
					<div class="flex flex-col lg:flex-row items-center p-5">
						<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#EB131333] rounded-full p-2">
							<img
								alt="Cliquez pour choisir la demande d'immatriculation"
								class=""
								src="@/assets/images/parc/file_pen.png"
							/>
							<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
								<i class="fa-light fa-check"></i>
							</span>
						</div>
						<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
							<h2 class="font-semibold">Dépôt de titre</h2>
							<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
						</div>
					</div>
				</label>
			</div>
		</div>

		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="custom-checkbox-2"
					v-model="repriseType"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="custom-checkbox"
					type="radio"
					value="reprise"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="custom-checkbox-2"
				>
					<div class="flex flex-col lg:flex-row items-center p-5">
						<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#1371EB33] rounded-full p-2">
							<img
								alt="Cliquez pour choisir la demande d'immatriculation"
								class=""
								src="@/assets/images/parc/file_blue.png"
							/>
							<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
								<i class="fa-light fa-check"></i>
							</span>
						</div>
						<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
							<h2 class="font-semibold">Reprise de titre</h2>
							<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
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
						<label class="form-label" for="vin-form-1">Entrer le VIN du véhicule</label>
						<input
							id="vin-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="13254624162"
						/>
					</div>
				</div>
			</ModalBody>
			<ModalFooter class="">
				<div class="flex mb-2">
					<button class="btn btn-primary w-full" type="button" @click="closeModal">Suivant</button>
				</div>
			</ModalFooter>
		</Modal>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import { useRouter } from "vue-router";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";

	const repriseType = ref("deposit");
	const router = useRouter();
	const modalIsOpen = ref(false);

	const goNext = () => {
		if (repriseType.value === "deposit") {
			router.push("/affiliate/dashboard/reprise/CarChoice");
		} else {
			modalIsOpen.value = true;
		}
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		router.push("/affiliate/dashboard/reprise/VehicleInfo");
	};
</script>
