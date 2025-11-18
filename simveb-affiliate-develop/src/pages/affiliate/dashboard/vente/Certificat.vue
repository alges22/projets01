<template>
	<div class="justify-center flex mt-8 bg-white p-4">
		<div class="border border-gray-200 md:w-2/3 w-full mx-auto">
			<div class="p-5 text-center">
				<div class="text-4xl font-bold leading-10 uppercase tracking-wide">Certificat de vente</div>
				<div class="text-lg font-bold leading-5 mt-4 uppercase tracking-wide">
					au profit de l'institution financière
				</div>
				<div class="text-base font-normal leading-5 mt-8 tracking-wide text-primary">[Nom de la banque]</div>
			</div>

			<div class="text-left mt-8 p-4 text-base leading-8">
				<p>Véhicule :</p>
				<p>Immatriculation/VIN :</p>
				<p>Propriétaire :</p>
				<p>Date d'émission :</p>
				<p>Date de validité :</p>
				<p>Numéro de référence :</p>
			</div>

			<div class="p-4">
				<img
					class="ml-auto w-24 h-24 object-cover rounded-xl"
					src="@/assets/images/qr_code.svg"
					alt="vehicle"
				/>
			</div>
		</div>
	</div>

	<div class="text-right mt-5">
		<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">Retour</button>
		<button class="btn btn-primary w-48" type="button" @click="$router.push('/affiliate/dashboard')">
			Imprimer
		</button>
	</div>
</template>

<script setup></script>

<style scoped></style>
