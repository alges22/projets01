<template>
	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div>
						<label class="form-label" for="crud-form-1">Nom du propriétaire</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="KOKOU"
						/>
					</div>

					<div>
						<label class="form-label" for="crud-form-1">Prénom du propriétaire</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="Anani"
						/>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<label class="form-label" for="crud-form-1">Nom du conducteur</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="ADENIYI"
						/>
					</div>

					<div>
						<label class="form-label" for="crud-form-1">Prénom du conducteur</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="Ichola"
						/>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<label class="form-label" for="crud-form-1">Numéro de permis de conduire</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="123456789"
						/>
					</div>

					<div>
						<label class="form-label">Photo de permis de conduire</label>
						<label
							class="flex rounded-lg border bg-[#F4F6F9] px-4 py-2 items-center cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500"
							for="file-upload"
						>
							<i class="text-[#6D7580] me-8 fa-solid text-2xl fa-cloud-arrow-up"></i>
							<div class="flex text-sm leading-5 text-dark">
								<div class="relative rounded-md font-semibold">
									<span>Sélectionner le fichier</span>
								</div>
							</div>
						</label>
						<input id="file-upload" class="sr-only" name="file-upload" type="file" />
						<p class="mt-1 text-xs leading-4 text-gray-600">
							Format pris en compte: png, jpg - Taille : 500 Ko
						</p>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<label class="form-label" for="crud-form-1">Type de passage</label>
						<select aria-label="Default select example" class="form-select">
							<option>Entrée</option>
							<option>Sortie</option>
						</select>
					</div>

					<div>
						<label class="form-label" for="crud-form-1">Nombre de personnes à bord</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="number"
							value="4"
						/>
					</div>
				</div>
			</div>

			<div class="text-right mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
					Annuler
				</button>
				<button class="btn btn-primary w-36" type="button" @click="successModalIsOpen = true">
					Enregistrer
				</button>
			</div>
		</div>

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
					<div class="text-2xl mt-5">Le véhicule a été enregistré avec succès !</div>
				</div>
				<div class="px-5 pb-8 text-center mt-4">
					<button class="btn btn-primary w-full mx-2" type="button" @click="closeModal">
						Ok, j'ai compris !
					</button>
				</div>
			</ModalBody>
		</Modal>
	</div>
</template>

<script setup>
	import { Modal, ModalBody } from "@/global-components/modal/index.js";
	import { ref } from "vue";
	import { useRouter } from "vue-router";

	const router = useRouter();
	const successModalIsOpen = ref(false);

	const closeModal = () => {
		successModalIsOpen.value = false;
		router.push("/police/dashboard");
	};
</script>
