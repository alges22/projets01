<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<BackButton />

			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Faire une demande</li>
				</ol>
			</nav>
		</div>

		<div class="intro-y flex items-center mt-8">
			<h2 class="text-lg font-semibold mr-auto">Sélectionner le type de service</h2>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5">
			<div class="intro-y col-span-12 md:col-span-6">
				<div class="relative group">
					<input
						id="custom-checkbox-1"
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="demand"
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
									src="../../../assets/images/parc/plaque.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Demande d'immatriculation</h2>
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
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="declaration"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-2"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7505033] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="../../../assets/images/parc/hand-crosssing.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Déclaration de gage</h2>
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
					<button class="btn btn-primary w-36" type="button" @click="goNext">Suivant</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import { useRouter } from "vue-router";
	import BackButton from "@/components/BackButton.vue";

	const serviceType = ref("demand");
	const router = useRouter();

	const goNext = () => {
		if (serviceType.value === "demand") {
			router.push("/parc/dashboard/immatriculation/VehiculeType");
		} else {
			router.push("/parc/dashboard/declaration/BankSelection");
		}
	};
</script>
