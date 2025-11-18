<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<BackButton />

			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Vérification des immatriculations</li>
				</ol>
			</nav>
		</div>

		<div class="flex justify-between items-end dashboard-card !rounded-md mt-8 lg:mt-16">
			<div class="w-full">
				<label class="form-label" for="crud-form-1">Entrer le numéro d'immatriculation de la voiture</label>
				<input id="crud-form-1" class="form-control w-full" placeholder="Input text" type="text" value="00XY" />
			</div>
			<div class="ms-2 w-1/5">
				<button class="btn btn-primary w-full h-auto" type="button">Vérifier</button>
			</div>
		</div>

		<div class="col-span-12 mt-8">
			<Alert class="alert-outline-success bg-green-50 flex items-center mb-2 font-bold">
				<i class="fa-solid text-xl fa-check-circle w-6 h-6 mr-4" />
				<span class="text-dark">Ce véhicule est en règle</span>
			</Alert>
		</div>

		<div class="intro-y box mt-8 rounded-xl border-success border">
			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold">Informations sur le Véhicule</h2>
				<div class="flex flex-col lg:flex-row px-4 pt-4 pb-0">
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Marque</p>
							<p class="text-dark font-medium text-sm">Berline</p>
						</div>
						<div class="mt-4">
							<p class="text-success font-semibold leading-7">Pays</p>
							<p class="text-dark font-medium text-sm">Bénin</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Immatriculation</p>
							<p class="text-dark font-medium text-sm">00XY</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Numéro de châssis</p>
							<p class="text-dark font-medium text-sm">8392OIEHOH</p>
						</div>
					</div>
				</div>
			</div>
			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold">Information sur le propriétaire</h2>
				<div class="flex flex-col lg:flex-row px-4 pt-4 pb-0">
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Nom et Prénom ou Raison social</p>
							<p class="text-dark font-medium text-sm">ADENIYI Ichola</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Téléphone</p>
							<p class="text-dark font-medium text-sm">+229 95 99 08 58</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Pays de naissance</p>
							<p class="text-dark font-medium text-sm">Bénin</p>
						</div>
					</div>
				</div>
			</div>
			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<h2 class="text-lg leading-7 font-bold">Information sur l’officier ayant enregistrer</h2>
				<div class="flex flex-col lg:flex-row px-4 pt-4 pb-0">
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Nom et Prénoms</p>
							<p class="text-dark font-medium text-sm">KAKPO Brice</p>
						</div>
					</div>
					<div class="mt-6 lg:mt-0 flex-1 px-5 pt-5 lg:pt-0">
						<div>
							<p class="text-success font-semibold leading-7">Matricule</p>
							<p class="text-dark font-medium text-sm">002121232</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button
					class="btn btn-outline-danger mr-4 border-2"
					type="button"
					@click="$router.push('/signalement')"
				>
					Signaler la voiture
				</button>
				<button class="btn btn-primary w-36" type="button" @click="$router.push('/police/verification')">
					OK
				</button>
			</div>
		</div>
	</div>
</template>

<script setup>
	import Alert from "@/global-components/alert/index.js";
	import BackButton from "@/components/BackButton.vue";
</script>

<style scoped></style>
