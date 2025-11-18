<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">
			Veuillez sélectionner le type de véhicule que vous souhaitez transformer
		</h2>
	</div>

	<div class="flex items-center justify-center dashboard-card mb-4 mt-8">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" checked />
		</div>
	</div>

	<div class="flex items-center justify-center dashboard-card my-4">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" />
		</div>
	</div>

	<div class="flex items-center justify-center dashboard-card my-4">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" />
		</div>
	</div>

	<div class="flex items-center justify-center dashboard-card my-4">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" />
		</div>
	</div>

	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
				Annuler
			</button>
			<button class="btn btn-primary w-36" type="button" @click="modalIsOpen = true">Suivant</button>
		</div>
	</div>

	<Modal :show="modalIsOpen" @hidden="modalIsOpen = false">
		<ModalHeader class="pt-6">
			<h2 class="font-bold text-base mr-auto">Vérification du code OTP</h2>
			<button class="absolute right-0 top-0 mt-6 mr-3" @click="modalIsOpen = false">
				<i class="fa-solid fa-x w-8 h-4 font-bold" />
			</button>
		</ModalHeader>
		<ModalBody>
			<div class="flex flex-col space-y-5">
				<p class="font-bold">Entrez le code de vérification envoyé par SMS au numéro de téléphone ******31.</p>
				<InputCode :length="5" class="mx-6" />

				<div class="flex flex-col space-y-5">
					<div class="flex flex-row items-center justify-center text-center text-sm font-bold space-x-1">
						<p>Vous n’avez pas reçu le code ?</p>
						<a
							class="flex flex-row items-center text-blue-600"
							href="http://"
							rel="noopener noreferrer"
							target="_blank"
						>
							Renvoyer le code
						</a>
					</div>
					<div>
						<button class="btn btn-primary w-full mx-2" type="button" @click="openSuccessModal">
							Prendre en charge le dossier
						</button>
					</div>
				</div>
			</div>
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
				<div class="text-2xl mt-5">Le dossier a été pris en charge avec succès !</div>
			</div>
			<div class="px-5 pb-8 text-center mt-4">
				<button class="btn btn-primary w-full mx-2" type="button" @click="closeModal">Suivant</button>
			</div>
		</ModalBody>
	</Modal>
</template>

<script setup>
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import InputCode from "@/components/Form/InputCode.vue";
	import { useRouter } from "vue-router";
	import { ref } from "vue";

	const router = useRouter();

	const modalIsOpen = ref(false);
	const successModalIsOpen = ref(false);

	const openSuccessModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = true;
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = false;
		router.push("/affiliate/dashboard/transformation/TransformationType");
	};
</script>

<style scoped></style>
