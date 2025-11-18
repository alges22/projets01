<template>
	<template v-if="loaded">
		<div class="justify-center flex mt-8">
			<div class="dashboard-card md:w-2/3 w-full mx-auto">
				<div class="p-5 text-center">
					<svg
						class="lucide lucide-check-circle w-24 h-24 text-success mx-auto mt-3"
						fill="none"
						height="32"
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						viewBox="0 0 24 24"
						width="32"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
					<div class="text-2xl mt-5 font-bold text-primary">
						Certificat de cession confirmé !
						<div class="w-1/3 bg-gray-400 h-px mt-2 m-auto"></div>
					</div>
				</div>

				<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
					<p class="col-span-12 w-full text-lg leading-5">
						Ce véhicule a été vendu par M. Gbefa Toni à M. JOHADBEDJI Jule
					</p>
					<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
						<div class="intro-y flex items-center">
							<h2 class="text-primary text-2xl">Vendeur</h2>
						</div>
					</div>
					<div class="col-span-12 grid grid-cols-12 gap-6">
						<div class="col-span-12 mx-2 lg:mx-9 intro-y">
							<div class="flex items-center pb-2 mt-2 border-b">
								<p class="w-1/2 text-lg leading-5">Nom & Prénom</p>
								<p class="font-bold text-dark w-1/2 text-lg leading-5">Gbefa Toni</p>
							</div>
						</div>
					</div>

					<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
						<div class="intro-y flex items-center">
							<h2 class="text-primary text-2xl">Acheteur</h2>
						</div>
					</div>
					<div class="col-span-12 grid grid-cols-12 gap-6">
						<div class="col-span-12 mx-2 lg:mx-9 intro-y">
							<div class="flex items-center pb-2 mt-2 border-b">
								<p class="w-1/2 text-lg leading-5">Nom & Prénom</p>
								<p class="font-bold text-dark w-1/2 text-lg leading-5">JOHADBEDJI Jule</p>
							</div>
						</div>
					</div>

					<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
						<div class="intro-y flex items-center">
							<h2 class="text-primary text-2xl">Véhicule</h2>
						</div>
					</div>
					<div class="col-span-12 grid grid-cols-12 gap-6">
						<div class="col-span-12 mx-2 lg:mx-9 intro-y">
							<div class="flex items-center pb-2 mt-6 border-b">
								<p class="w-1/2 text-lg leading-5">Marque</p>
								<p class="font-bold text-dark w-1/2 text-lg leading-5">BMW</p>
							</div>
							<div class="flex items-center pb-2 mt-6 border-b">
								<p class="w-1/2 text-lg leading-5">Modèle de véhicule</p>
								<p class="font-bold text-dark w-1/2 text-lg leading-5">BMW Série 1 M135i xDrive</p>
							</div>
							<div class="flex items-center pb-2 mt-2 border-b">
								<p class="w-1/2 text-lg leading-5">Immatriculation</p>
								<p class="font-bold text-dark w-1/2 text-lg leading-5">CA 5021</p>
							</div>
							<div class="flex items-center pb-2 mt-6 border-b">
								<p class="w-1/2 text-lg leading-5">Numéro du châssis</p>
								<p class="font-bold text-dark w-1/2 text-lg leading-5">JMKL02145578784777878</p>
							</div>
						</div>
					</div>
				</div>

				<div class="p-5 text-center mt-12">
					<button
						class="btn btn-primary w-full"
						type="button"
						@click="$router.push('/affiliate/dashboard/mutation/VehicleInfo')"
					>
						Suivant
					</button>
				</div>
			</div>
		</div>
	</template>

	<Modal :show="askModalIsOpen" backdrop="static" @hidden="askModalIsOpen = false">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div>
					<label class="form-label" for="crud-form-1">Entrer le numéro du certificat de cession</label>
					<input
						id="crud-form-1"
						class="form-control w-full"
						placeholder="Input text"
						type="text"
						value="2656166527"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-primary w-full" type="button" @click="load">Vérifier</button>
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
