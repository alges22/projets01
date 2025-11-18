<template>
	<div class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner le type de véhicule</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="custom-checkbox-1"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="custom-checkbox"
					type="radio"
					checked
					value="1"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="custom-checkbox-1"
				>
					<div class="flex flex-col lg:flex-row items-center p-5">
						<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
							<img
								alt="Cliquez pour choisir la demande d'immatriculation"
								class=""
								src="../../../../assets/images/parc/car_blue.png"
							/>
							<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
								<i class="fa-light fa-check"></i>
							</span>
						</div>
						<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
							<h2 class="font-semibold">Véhicule à 4 roues ou plus</h2>
							<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
						</div>
					</div>
				</label>
			</div>
		</div>

		<div class="intro-y col-span-12 md:col-span-6">
			<div class="relative group">
				<input
					id="custom-checkbox-2"
					class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
					name="custom-checkbox"
					type="radio"
					value="false"
				/>
				<label
					class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
					for="custom-checkbox-2"
				>
					<div class="flex flex-col lg:flex-row items-center p-5">
						<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F9933933] rounded-full p-2">
							<img
								alt="Cliquez pour choisir la demande d'immatriculation"
								class=""
								src="../../../../assets/images/parc/bike_2.png"
							/>
							<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
								<i class="fa-light fa-check"></i>
							</span>
						</div>
						<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
							<h2 class="font-semibold">Véhicule à 2 ou 3 roues</h2>
							<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
						</div>
					</div>
				</label>
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
					@click="$router.push('/parc/dashboard/immatriculation/VerifInfoVehicule')"
				>
					Suivant
				</button>
			</div>
		</div>
	</div>
</template>

<script setup></script>
