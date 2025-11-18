<template>
	<div class="intro-y flex items-center mt-8 lg:mt-16">
		<h2 class="text-lg font-semibold mr-auto">Vérifier les informations de l’acheteur</h2>
	</div>

	<div class="flex justify-between items-end dashboard-card !rounded-md mt-4">
		<div class="w-full">
			<label class="form-label font-bold" for="crud-form-1">Entrer le NIP ou l’IFU de l’acheteur</label>
			<input id="crud-form-1" class="form-control w-full" placeholder="Input text" type="text" value="00XY" />
		</div>
		<div class="ms-2 w-1/5">
			<button class="btn btn-primary w-full h-auto" type="button">Vérifier</button>
		</div>
	</div>

	<div class="intro-y box mt-8 rounded-lg">
		<div v-show="isLoaded" class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<h2 class="text-lg leading-7 font-bold">Informations sur le Véhicule</h2>
			<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7">Marque</p>
						<p class="text-dark font-medium text-sm">Berline</p>
					</div>
					<div class="mt-4">
						<p class="font-semibold leading-7">Pays</p>
						<p class="text-dark font-medium text-sm">Bénin</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7">Immatriculation</p>
						<p class="text-dark font-medium text-sm">00XY</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7">Numéro de châssis</p>
						<p class="text-dark font-medium text-sm">8392OIEHOH</p>
					</div>
				</div>
			</div>
		</div>

		<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
			<h2 class="text-lg leading-7 font-bold">Information sur le propriétaire</h2>
			<div class="flex flex-col lg:flex-row px-2 pt-4 pb-0">
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7">Nom et Prénom ou Raison social</p>
						<p class="text-dark font-medium text-sm">ADENIYI Ichola</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7">Téléphone</p>
						<p class="text-dark font-medium text-sm">+229 95 99 08 58</p>
					</div>
				</div>
				<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
					<div>
						<p class="font-semibold leading-7">Pays de naissance</p>
						<p class="text-dark font-medium text-sm">Bénin</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
				Annuler
			</button>
			<button
				v-if="isLoaded"
				class="btn btn-primary mr-4 border-2 w-48"
				type="button"
				@click="$router.push('/affiliate/dashboard/prestige/unregistered/PlateCaracteristicForm')"
			>
				Suivant
			</button>
			<button v-else class="btn btn-primary mr-4 border-2 w-48" type="button" @click="modalIsOpen = true">
				Valider
			</button>
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
							Valider
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
	import { ref } from "vue";
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import InputCode from "@/components/Form/InputCode.vue";

	const modalIsOpen = ref(false);
	const successModalIsOpen = ref(false);
	const isLoaded = ref(false);

	const openSuccessModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = true;
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = false;
		isLoaded.value = true;
	};
</script>

<style scoped></style>
