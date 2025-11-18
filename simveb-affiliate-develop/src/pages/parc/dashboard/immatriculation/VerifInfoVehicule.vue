<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Vérifier les informations du véhicule</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div>
						<label class="form-label" for="crud-form-1">Entrer le VIN du véhicule</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="VN1324718265422NVV  "
						/>
					</div>

					<div>
						<label class="form-label" for="crud-form-1">Entrer le numéro de la quittance de douane</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="19397389222"
						/>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<label class="form-label" for="crud-form-1">Entrer le numéro de référence SESUR</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="VN1324718265422NVV"
						/>
					</div>

					<div>
						<label class="form-label" for="crud-form-1">Entrer le NIP ou l’IFU de l’acheteur</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="13254624162"
						/>
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
					@click="$router.push('/parc/dashboard/immatriculation/VehicleInfo')"
				>
					Vérifier
				</button>
			</div>
		</div>
	</div>
</template>

<script setup></script>
