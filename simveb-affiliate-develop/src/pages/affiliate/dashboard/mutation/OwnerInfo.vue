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
				<button
					class="btn btn-primary w-36"
					type="button"
					@click="$router.push('/affiliate/dashboard/mutation/CertificatInfo')"
				>
					Suivant
				</button>
			</div>
		</div>
	</template>

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
	import { onMounted, ref } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";

	const askModalIsOpen = ref(false);
	const loaded = ref(false);

	onMounted(() => {
		askModalIsOpen.value = true;
	});

	const load = () => {
		askModalIsOpen.value = false;
		loaded.value = true;
	};
</script>

<style scoped></style>
