<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner les caractéristiques de la plaque</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="mb-8">
					<div>
						<label class="form-label">Entrer le label voulu</label>
						<input type="text" class="form-control valid:border-success" value="AA22" />
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div>
						<label class="form-label">Type d’immatriculation</label>
						<select aria-label="Default select example" class="form-select">
							<option>Normal</option>
							<option>Anormal (je sais pas quoi mettre)</option>
						</select>
					</div>
					<div>
						<label class="form-label">Forme de plaque</label>
						<select aria-label="Default select example" class="form-select">
							<option>Rectangulaire</option>
							<option>Carré</option>
						</select>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<label class="form-label">Couleur de plaque</label>
						<select aria-label="Default select example" class="form-select">
							<option>Grise</option>
							<option>C'était</option>
							<option>mieux</option>
							<option>quand</option>
							<option>il</option>
							<option>fallait</option>
							<option>juste</option>
							<option>cliquer sur une</option>
							<option>la couleur</option>
						</select>
					</div>
					<div>
						<label class="form-label">Pièces jointes</label>
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
			</div>

			<div class="text-right mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
					Retour
				</button>
				<button class="btn btn-primary w-36" type="submit" @click.prevent="modalIsOpen = true">Valider</button>
			</div>
		</form>
	</div>

	<Modal :show="modalIsOpen" size="modal-xl" @hidden="modalIsOpen = false">
		<ModalHeader class="pt-6">
			<h2 class="font-bold text-base mr-auto">Passer au paiement</h2>
			<button class="absolute right-0 top-0 mt-6 mr-3" @click="modalIsOpen = false">
				<i class="fa-solid fa-x w-8 h-4 font-bold" />
			</button>
		</ModalHeader>
		<ModalBody class="grid grid-cols-12 gap-4 gap-y-3">
			<table class="table col-span-12 table-bordered">
				<thead class="table-light">
					<tr class="text-sm">
						<th class="whitespace-nowrap">Intitulé</th>
						<th class="whitespace-nowrap">Quantité</th>
						<th class="whitespace-nowrap">Prix</th>
					</tr>
				</thead>
				<tbody>
					<tr class="text-base">
						<td class="whitespace-nowrap font-medium">Frais de dossier</td>
						<td class="whitespace-nowrap font-medium">1</td>
						<td class="whitespace-nowrap font-medium">150 000 FCFA</td>
					</tr>
				</tbody>
				<tfoot>
					<tr class="bg-[#EEF8FE]">
						<td colspan="3" class="font-bold text-right text-lg">Total : 150 000 FCFA</td>
					</tr>
				</tfoot>
			</table>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-12">
				<button class="btn btn-outline-primary w-1/2 mr-4 text-base" type="button" @click="modalIsOpen = false">
					Plus tard
				</button>
				<button class="btn btn-primary w-1/2 text-base" type="button" @click="goNext">Payer maintenant</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import { ref } from "vue";
	import { useRouter } from "vue-router";

	const router = useRouter();
	const modalIsOpen = ref(false);

	const goNext = () => {
		modalIsOpen.value = false;
		router.push("/affiliate/PaymentSuccess");
	};
</script>
