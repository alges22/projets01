<template>
	<template v-if="loaded">
		<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary text-2xl">Informations du propriétaire</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Nom</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">Gbefa</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Prénoms</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">Toni</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Date de naissance</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">02/07/1998</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Lieu de naissance</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">Cotonou</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Nationalité</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">Architecture industrielle</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Sexe</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">M</p>
					</div>
				</div>

				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">NPI</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">0254054889</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Référence du BFU</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">0125465564523213</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Téléphone</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">00229 29 99 99 92</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Adresse email</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">Gbefa@gmail.com</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2 text-lg leading-5">Domicile</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">Cadjehoun Carré. 1144</p>
					</div>
				</div>
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
	</template>

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
				<div class="text-2xl mt-5">Véhicule vérifié avec succès !</div>
			</div>
			<div class="px-5 pb-8 text-center mt-4">
				<button class="btn btn-primary w-full mx-2" type="button" @click="closeModal">Suivant</button>
			</div>
		</ModalBody>
	</Modal>

	<Modal :show="askModalIsOpen" @hidden="askModalIsOpen = false">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div>
					<label class="form-label" for="crud-form-1">Entrer le NPI de l'acheteur</label>
					<input
						id="crud-form-1"
						class="form-control w-full"
						placeholder="Input text"
						type="text"
						value="123456789"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-primary w-full" type="button" @click="load">Suivant</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { useRouter } from "vue-router";
	import { onMounted, ref } from "vue";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import InputCode from "@/components/Form/InputCode.vue";

	const router = useRouter();

	const modalIsOpen = ref(false);
	const successModalIsOpen = ref(false);
	const askModalIsOpen = ref(false);
	const loaded = ref(false);

	const openSuccessModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = true;
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = false;
		router.push("/affiliate/dashboard/vente/CarChoice");
	};

	onMounted(() => {
		askModalIsOpen.value = true;
	});

	const load = () => {
		askModalIsOpen.value = false;
		loaded.value = true;
	};
</script>

<style scoped></style>
