<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Entrer les informations du véhicule</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12" @submit.prevent="submit">
			<div class="intro-y mb-4">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div class="relative group">
						<input
							id="car-type-0"
							v-model="demand.is_car"
							class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
							name="car_type"
							type="radio"
							:value="1"
						/>
						<label
							class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
							for="car-type-0"
						>
							<span class="flex flex-col lg:flex-row items-center p-5">
								<span class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
									<img
										alt="Cliquez pour choisir la demande d'immatriculation"
										class=""
										src="@/assets/images/parc/car_blue.png"
									/>
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</span>
								<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
									<h2 class="font-semibold">Véhicule à 4 roues ou plus</h2>
									<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
								</div>
							</span>
						</label>
					</div>

					<div class="relative group">
						<input
							id="car-type-1"
							v-model="demand.is_car"
							class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
							name="car_type"
							type="radio"
							:value="0"
						/>
						<label
							class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
							for="car-type-1"
						>
							<span class="flex flex-col lg:flex-row items-center p-5">
								<span class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F9933933] rounded-full p-2">
									<img
										alt="Cliquez pour choisir la demande d'immatriculation"
										class=""
										src="@/assets/images/parc/bike_2.png"
									/>
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</span>
								<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
									<h2 class="font-semibold">Véhicule à 2 ou 3 roues</h2>
									<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
								</div>
							</span>
						</label>
					</div>
				</div>
			</div>

			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div>
						<TextInputGroup
							v-model="demand.vin"
							name="vin"
							label="Entrer le VIN du véhicule"
							placeholder="VN1324718265422NVV"
							add-class="w-full"
							:disabled="loading"
							:errors="errors.vin"
							required
							auto-complete="vin"
						/>
					</div>

					<div>
						<TextInputGroup
							v-model="demand.customs_ref"
							name="customs_ref"
							label="Entrer le numéro de la quittance de douane"
							placeholder="VN1324718265422NVV"
							add-class="w-full"
							:disabled="loading"
							:errors="errors.customs_ref || []"
							required
						/>
					</div>
				</div>
			</div>

			<div class="flex align-center justify-end mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Retour
				</button>
				<BasicButton class="btn-primary w-36" type="submit" :loading="loading"> Suivant</BasicButton>
			</div>
		</form>
	</div>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, loading, errors } = storeToRefs(demandStore);

	const submit = () => {
		demandStore.fetchVehicleInfo(demand.value.vin, demand.value.customs_ref, demand.value.is_car).then(() => {
			emit("next");
		});
	};
</script>
