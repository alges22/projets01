<template>
	<div class="intro-y flex items-center justify-center mt-8">
		<h2 class="text-lg font-semibold">Veuillez sélectionner le type de véhicule que vous souhaitez transformer</h2>
	</div>

	<div class="flex items-center justify-center dashboard-card mb-4 mt-8">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" checked />
		</div>
	</div>

	<div class="flex items-center justify-center dashboard-card my-4">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" />
		</div>
	</div>

	<div class="flex items-center justify-center dashboard-card my-4">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" />
		</div>
	</div>

	<div class="flex items-center justify-center dashboard-card my-4">
		<div class="w-1/4 p-3.5">
			<img class="w-24 h-24 object-cover rounded-xl" src="@/assets/images/parc/car_red_2.png" alt="vehicle" />
		</div>
		<div class="w-1/4 p-3.5">
			<span class="text-lg">VCF534352672664</span>
		</div>
		<div class="w-1/4 p-3.5 text-center">
			<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">CB 0124</span>
		</div>
		<div class="form-check ml-auto mr-4">
			<input class="form-check-input" name="car-choice" type="radio" />
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
				@click="$router.push('/affiliate/dashboard/vente/BuyerForm')"
			>
				Suivant
			</button>
		</div>
	</div>
</template>

<script setup></script>

<style scoped></style>
