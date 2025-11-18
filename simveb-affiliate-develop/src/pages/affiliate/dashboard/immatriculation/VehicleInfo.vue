<template>
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
	</div>
	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
				Annuler
			</button>
			<button
				class="btn btn-primary w-36"
				type="button"
				@click="$router.push('/affiliate/dashboard/immatriculation/PlateCaracteristicForm')"
			>
				Suivant
			</button>
		</div>
	</div>
</template>

<script setup></script>

<style scoped></style>
