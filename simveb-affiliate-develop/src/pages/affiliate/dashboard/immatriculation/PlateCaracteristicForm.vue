<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner les caractéristiques de la plaque</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div>
						<label class="form-label">Forme de plaque</label>
						<select aria-label="Default select example" class="form-select">
							<option>Rectangulaire</option>
							<option>Carré</option>
						</select>
					</div>

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
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<label class="form-label">Type d’immatriculation</label>
						<select aria-label="Default select example" class="form-select">
							<option>Normal</option>
							<option>Anormal (je sais pas quoi mettre)</option>
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
				<button
					class="btn btn-primary w-36"
					type="button"
					@click="$router.push('/affiliate/dashboard/immatriculation/Recap')"
				>
					Valider
				</button>
			</div>
		</div>
	</div>
</template>

<script setup></script>
