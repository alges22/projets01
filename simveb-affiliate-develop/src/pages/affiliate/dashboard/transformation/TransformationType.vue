<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner le type de service</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12 md:col-span-6 relative mr-2 p-0 bg-white rounded-lg">
			<CustomCheckBoxInput
				id="custom-checkbox-1"
				name="customer-checkbox"
				input-class="text-gray-500 w-6 h-6 top-10 right-4 checked:bg-blue-500 checked:border-transparent"
				label-class="w-full flex flex-row justify-between items-center rounded-lg px-4 peer-focus:outline-none peer-checked:border-success peer-checked:border hover:bg-blue-100"
				type="radio"
			>
				<div class="flex flex-col lg:flex-row items-center p-5 pl-0">
					<img
						alt="Cliquez pour choisir la demande d'immatriculation"
						class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
						src="@/assets/images/parc/design_pattern.png"
					/>
					<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
						<h2 class="font-semibold">Esthétique/Design</h2>
					</div>
				</div>
			</CustomCheckBoxInput>
		</div>
		<div class="intro-y col-span-12 md:col-span-6 relative mr-2 p-0 bg-white rounded-lg">
			<CustomCheckBoxInput
				id="custom-checkbox-2"
				name="customer-checkbox"
				input-class="text-gray-500 w-6 h-6 top-10 right-4 checked:bg-blue-500 checked:border-transparent"
				label-class="w-full flex flex-row justify-between items-center rounded-lg px-4 peer-focus:outline-none peer-checked:border-success peer-checked:border hover:bg-blue-100"
				type="radio"
			>
				<div class="flex flex-col lg:flex-row items-center p-5 pl-0">
					<img
						alt="Cliquez pour choisir la demande d'immatriculation"
						class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
						src="@/assets/images/parc/screw_car.png"
					/>
					<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
						<h2 class="font-semibold">Performances mécaniques</h2>
					</div>
				</div>
			</CustomCheckBoxInput>
		</div>

		<div class="intro-y col-span-12 md:col-span-6 relative mr-2 p-0 bg-white rounded-lg">
			<CustomCheckBoxInput
				id="custom-checkbox-3"
				name="customer-checkbox"
				input-class="text-gray-500 w-6 h-6 top-10 right-4 checked:bg-blue-500 checked:border-transparent"
				label-class="w-full flex flex-row justify-between items-center rounded-lg px-4 peer-focus:outline-none peer-checked:border-success peer-checked:border hover:bg-blue-100"
				type="radio"
			>
				<div class="flex flex-col lg:flex-row items-center p-5 pl-0">
					<img
						alt="Cliquez pour choisir la demande d'immatriculation"
						class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
						src="@/assets/images/parc/truc_opened.png"
					/>
					<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
						<h2 class="font-semibold">Personnalisation thématique</h2>
					</div>
				</div>
			</CustomCheckBoxInput>
		</div>

		<div class="intro-y col-span-12 md:col-span-6 relative mr-2 p-0 bg-white rounded-lg">
			<CustomCheckBoxInput
				id="custom-checkbox-4"
				name="customer-checkbox"
				input-class="text-gray-500 w-6 h-6 top-10 right-4 checked:bg-blue-500 checked:border-transparent"
				label-class="w-full flex flex-row justify-between items-center rounded-lg px-4 peer-focus:outline-none peer-checked:border-success peer-checked:border hover:bg-blue-100"
				type="radio"
			>
				<div class="flex flex-col lg:flex-row items-center p-5 pl-0">
					<img
						alt="Cliquez pour choisir la demande d'immatriculation"
						class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
						src="@/assets/images/parc/car_lights.png"
					/>
					<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
						<h2 class="font-semibold">Éclairage</h2>
					</div>
				</div>
			</CustomCheckBoxInput>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
					Annuler
				</button>
				<button
					class="btn btn-primary w-36"
					type="button"
					@click="$router.push('/affiliate/dashboard/transformation/PersonalisationForm')"
				>
					Suivant
				</button>
			</div>
		</div>
	</div>
</template>

<script setup>
	import CustomCheckBoxInput from "@/components/Form/CustomCheckBoxInput.vue";
</script>
