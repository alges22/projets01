<template>
	<SideBar>
		<li>
			<MenuLink :active="$route.path === '/interpol'" icon="gauge-max" label="Tableau de bord" to="/interpol" />
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="suitcase"
				label="Dossiers"
				to="interpol/application"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="book"
				label="Certificats de gages"
				to="bank/pledge"
			/>
		</li>
	</SideBar>

	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-5">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<BackButton />

				<ol class="breadcrumb">
					<li class="breadcrumb-item">Emettre un gage</li>
				</ol>
			</nav>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary">Informations du Véhicule</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2">Pays d'origine du véhicule</p>
						<p class="font-bold text-dark w-1/2">Canada</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Numéro du châssis</p>
						<p class="font-bold text-dark w-1/2">JMKL02145578784777878</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Marque</p>
						<p class="font-bold text-dark w-1/2">BMW</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Modèle de véhicule</p>
						<p class="font-bold text-dark w-1/2">BMW Série 1 M135i xDrive</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Energie</p>
						<p class="font-bold text-dark w-1/2">Essence</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Nbre de place assises</p>
						<p class="font-bold text-dark w-1/2">5 places max</p>
					</div>
				</div>

				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2">Numéro Moteur</p>
						<p class="font-bold text-dark w-1/2">Illisible</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Poids total (en Kg)</p>
						<p class="font-bold text-dark w-1/2">2400</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Poids à vide (en Kg)</p>
						<p class="font-bold text-dark w-1/2">1300</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Parc d'achat du véhicule</p>
						<p class="font-bold text-dark w-1/2">Wanted</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Année de 1ère mise en circulation</p>
						<p class="font-bold text-dark w-1/2">2014</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Type du véhicule</p>
						<p class="font-bold text-dark w-1/2">SUV</p>
					</div>
				</div>
			</div>

			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary">Informations du propriétaire</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2">Nom</p>
						<p class="font-bold text-dark w-1/2">Gbefa</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Prénoms</p>
						<p class="font-bold text-dark w-1/2">Toni</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Date de naissance</p>
						<p class="font-bold text-dark w-1/2">02/07/1998</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Lieu de naissance</p>
						<p class="font-bold text-dark w-1/2">Cotonou</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Nationalité</p>
						<p class="font-bold text-dark w-1/2">Architecture industrielle</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Sexe</p>
						<p class="font-bold text-dark w-1/2">M</p>
					</div>
				</div>

				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2">NPI</p>
						<p class="font-bold text-dark w-1/2">0254054889</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Référence du BFU</p>
						<p class="font-bold text-dark w-1/2">0125465564523213</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Téléphone</p>
						<p class="font-bold text-dark w-1/2">00229 29 99 99 92</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Adresse email</p>
						<p class="font-bold text-dark w-1/2">Gbefa@gmail.com</p>
					</div>
					<div class="flex items-center pb-2 mt-6 border-b">
						<p class="w-1/2">Domicile</p>
						<p class="font-bold text-dark w-1/2">Cadjehoun Carré. 1144</p>
					</div>
				</div>
			</div>
		</div>
		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
					Annuler
				</button>
				<button class="btn btn-primary w-36" type="button" @click="modalIsOpen = true">Confirmer</button>
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
					<p class="font-bold">
						Entrez le code de vérification envoyé par SMS au numéro de téléphone ******31.
					</p>
					<div class="flex flex-row items-center justify-between mx-6">
						<div class="w-16 h-16">
							<input
								id=""
								class="w-full h-full flex font-bold text-xl flex-col items-center justify-center text-center px-5 outline-none rounded-md border-2 border-gray-200 bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700"
								name=""
								type="text"
								value="8"
							/>
						</div>
						<div class="w-16 h-16">
							<input
								id=""
								class="w-full h-full flex font-bold text-xl flex-col items-center justify-center text-center px-5 outline-none rounded-md border-2 border-gray-200 bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700"
								name=""
								type="text"
								value="2"
							/>
						</div>
						<div class="w-16 h-16">
							<input
								id=""
								class="w-full h-full flex font-bold text-xl flex-col items-center justify-center text-center px-5 outline-none rounded-md border-2 border-gray-200 bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700"
								name=""
								type="text"
								value="8"
							/>
						</div>
						<div class="w-16 h-16">
							<input
								id=""
								class="w-full h-full flex font-bold text-xl flex-col items-center justify-center text-center px-5 outline-none rounded-md border-2 border-gray-200 bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700"
								name=""
								type="text"
								value="8"
							/>
						</div>
						<div class="w-16 h-16">
							<input
								id=""
								class="w-full h-full flex font-bold text-xl flex-col items-center justify-center text-center px-5 outline-none rounded-md border-2 border-gray-200 bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700"
								name=""
								type="text"
								value="5"
							/>
						</div>
					</div>

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
					<div class="text-2xl mt-5">Gage émis avec succès !</div>
					<div class="mt-2">Le dossier est en attente de validation par la banque.</div>
				</div>
				<div class="px-5 pb-8 text-center mt-4">
					<button class="btn btn-primary w-full mx-2" type="button" @click="closeModal">Suivant</button>
				</div>
			</ModalBody>
		</Modal>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import { useRouter } from "vue-router";

	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
	import BackButton from "@/components/BackButton.vue";

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
		router.push("/bank/pledge/PledgeApplication");
	};
</script>

<style scoped></style>
