<template>
	<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
		<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-2xl">Informations du Véhicule</h2>
			</div>
		</div>
		<div class="col-span-12 grid grid-cols-12 gap-6">
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Pays d'origine du véhicule</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">Canada</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Numéro du châssis</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">JMKL02145578784777878</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Marque</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">BMW</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Modèle de véhicule</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">BMW Série 1 M135i xDrive</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Énergie</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">Essence</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Nbre de place assises</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">5 places max</p>
				</div>
			</div>

			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Numéro Moteur</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">Illisible</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Poids total (en Kg)</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">2400</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Poids à vide (en Kg)</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">1300</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Parc d'achat du véhicule</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">Wanted</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Année de 1ère mise en circulation</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">2014</p>
				</div>
				<div class="flex items-center pb-2 mt-6 border-b">
					<p class="w-1/2 text-lg leading-5">Type du véhicule</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">SUV</p>
				</div>
			</div>
		</div>
	</div>
	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
				Annuler
			</button>
			<button class="btn btn-primary w-36" type="button" @click="firstModalIsOpen = true">Suivant</button>
		</div>
	</div>

	<Modal :show="firstModalIsOpen" @hidden="firstModalIsOpen = false">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div>
					<label class="form-label">
						Ce véhicule n'a pas encore fait l'objet d'un dépôt de titre. Veuillez entrer le numéro
						d'autorisation de réimmatriculation
					</label>
					<input class="form-control w-full" placeholder="Input text" type="text" value="13254624162" />
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-primary w-full" type="button" @click="openSecondModal">Vérifier</button>
			</div>
		</ModalFooter>
	</Modal>

	<Modal :show="secondModalIsOpen" backdrop="static" @hidden="secondModalIsOpen = false">
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
				<div class="text-2xl mt-5">Numéro d’autorisation de Ré-Immatriculation approuvé</div>
			</div>
			<div class="px-5 pb-8 text-center mt-4">
				<button class="btn btn-primary w-full mx-2" type="button" @click="openThirdModal">Continuer</button>
			</div>
		</ModalBody>
	</Modal>

	<Modal :show="thirdModalIsOpen" @hidden="thirdModalIsOpen = false">
		<ModalBody>
			<h4 class="text-lg text-center font-bold mb-8">Ré-Immatriculation simple ou de prestige ?</h4>
			<div class="flex flex-row justify-between mx-4">
				<div class="relative w-1/2 mr-2">
					<input
						id="custom-checkbox-1"
						class="text-primary focus:ring-violet-300 peer w-6 h-6 absolute top-5 left-4"
						name="custom-checkbox"
						type="radio"
						value="prestige"
					/>
					<label
						class="w-full h-[68px] cursor-pointer flex flex-row justify-between items-center border rounded-lg p-4 peer-focus:outline-none peer-focus:ring peer-checked:border-primary hover:bg-blue-100"
						for="custom-checkbox-1"
					>
						<div class="flex flex-row justify-between items-center ml-10 mr-4">
							<p class="text-lg text-gray-400">Prestige</p>
						</div>
					</label>
				</div>
				<div class="relative w-1/2 ml-2">
					<input
						id="custom-checkbox-2"
						class="text-primary focus:ring-violet-300 peer w-6 h-6 absolute top-5 left-4"
						name="custom-checkbox"
						type="radio"
						value="simple"
					/>
					<label
						class="w-full h-[68px] cursor-pointer flex flex-row justify-between items-center border rounded-lg p-4 peer-focus:outline-none peer-focus:ring peer-checked:border-primary hover:bg-blue-100"
						for="custom-checkbox-2"
					>
						<div class="flex flex-row justify-between items-center ml-10 mr-4">
							<p class="text-lg text-gray-400">Simple</p>
						</div>
					</label>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-primary w-full" type="button" @click="closeModal">Continuer</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { ref } from "vue";
	import { useRouter } from "vue-router";

	const router = useRouter();
	const firstModalIsOpen = ref(false);
	const secondModalIsOpen = ref(false);
	const thirdModalIsOpen = ref(false);

	const openSecondModal = () => {
		firstModalIsOpen.value = false;
		secondModalIsOpen.value = true;
	};

	const openThirdModal = () => {
		secondModalIsOpen.value = false;
		thirdModalIsOpen.value = true;
	};

	const closeModal = () => {
		thirdModalIsOpen.value = false;
		router.push("/affiliate/dashboard/reimmatriculation/PlateCaracteristicForm");
	};
</script>

<style scoped></style>
