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
						value="immatriculation"
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
									src="@/assets/images/parc/plaque.png"
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
						value="reimmatriculation"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-2"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#795D6833] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/reimat.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Demande de Ré-immatriculation</h2>
								<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
							</div>
						</div>
					</label>
				</div>
			</div>
			<div class="intro-y col-span-12 md:col-span-6">
				<div class="relative group">
					<input
						id="custom-checkbox-3"
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="prestige"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-3"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#FF474733] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/prestige.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Demande d’immatriculation de prestige</h2>
								<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
							</div>
						</div>
					</label>
				</div>
			</div>
			<div class="intro-y col-span-12 md:col-span-6">
				<div class="relative group">
					<input
						id="custom-checkbox-4"
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="reprise"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-4"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#3227AE33] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/reprise.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Dépôt ou une reprise de titres</h2>
								<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
							</div>
						</div>
					</label>
				</div>
			</div>
			<div class="intro-y col-span-12 md:col-span-6">
				<div class="relative group">
					<input
						id="custom-checkbox-5"
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="transformation"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-5"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#008CCF33] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/arrow_crossing.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Transformation de véhicule</h2>
								<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
							</div>
						</div>
					</label>
				</div>
			</div>
			<div class="intro-y col-span-12 md:col-span-6">
				<div class="relative group">
					<input
						id="custom-checkbox-6"
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="mutation"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-6"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#00AFB533] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/arrow_bue_green.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Demande de mutation</h2>
								<div class="text-slate-500 text-xs mt-0.5">Petite description</div>
							</div>
						</div>
					</label>
				</div>
			</div>
			<div class="intro-y col-span-12 md:col-span-6">
				<div class="relative group">
					<input
						id="custom-checkbox-7"
						v-model="serviceType"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="custom-checkbox"
						type="radio"
						value="vente"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						for="custom-checkbox-7"
					>
						<div class="flex flex-col lg:flex-row items-center p-5">
							<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7506E33] rounded-full p-2">
								<img
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/car_coin.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
							<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">Vente</h2>
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
					<button class="btn btn-primary w-36" type="button" @click="openModalOrNext">Suivant</button>
				</div>
			</div>
		</div>

		<Modal :show="modalIsOpen" @hidden="modalIsOpen = false">
			<ModalBody>
				<div class="grid grid-cols-12 gap-6 mt-5">
					<div class="intro-y col-span-12">
						<div class="relative group">
							<input
								id="user-checkbox-1"
								v-model="userType"
								class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
								name="user-checkbox"
								type="radio"
								value="physique"
							/>
							<label
								class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
								for="user-checkbox-1"
							>
								<div class="flex flex-col lg:flex-row items-center p-5">
									<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#EBC81333] rounded-full p-2">
										<img
											alt="Cliquez pour choisir la demande d'immatriculation"
											class=""
											src="@/assets/images/parc/user_sample.png"
										/>
										<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
											<i class="fa-light fa-check"></i>
										</span>
									</div>
									<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
										<h2 class="font-semibold">Personne physique</h2>
									</div>
								</div>
							</label>
						</div>
					</div>

					<div class="intro-y col-span-12">
						<div class="relative group">
							<input
								id="user-checkbox-2"
								v-model="userType"
								class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
								name="user-checkbox"
								type="radio"
								value="moral"
							/>
							<label
								class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
								for="user-checkbox-2"
							>
								<div class="flex flex-col lg:flex-row items-center p-5">
									<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#F7505033] rounded-full p-2">
										<img
											alt="Cliquez pour choisir la demande d'immatriculation"
											class=""
											src="@/assets/images/parc/building_sample.png"
										/>
										<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
											<i class="fa-light fa-check"></i>
										</span>
									</div>
									<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
										<h2 class="font-semibold">Personne moral</h2>
									</div>
								</div>
							</label>
						</div>
					</div>
				</div>
			</ModalBody>
			<ModalFooter class="">
				<div class="flex mb-2">
					<button class="btn btn-primary w-full" type="button" @click="closeModal">Suivant</button>
				</div>
			</ModalFooter>
		</Modal>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import { useRouter } from "vue-router";
	import BackButton from "@/components/BackButton.vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";

	const serviceType = ref("immatriculation");
	const modalIsOpen = ref(false);
	const userType = ref("physique");
	const router = useRouter();

	const goNext = () => {
		if (serviceType.value === "immatriculation") {
			router.push("/affiliate/dashboard/immatriculation/VehiculeType");
		} else if (serviceType.value === "reimmatriculation") {
			router.push("/affiliate/dashboard/reimmatriculation/OwnerInfo");
		} else if (serviceType.value === "prestige") {
			router.push("/affiliate/dashboard/prestige/ServiceType");
		} else if (serviceType.value === "reprise") {
			router.push("/affiliate/dashboard/reprise/OwnerInfo");
		} else if (serviceType.value === "transformation") {
			router.push("/affiliate/dashboard/transformation/CarChoice");
		} else if (serviceType.value === "mutation") {
			router.push("/affiliate/dashboard/mutation/OwnerInfo");
		} else if (serviceType.value === "vente") {
			router.push("/affiliate/dashboard/vente/OwnerInfo");
		} else {
			alert("Veuillez choisir un type de service");
		}
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		goNext();
	};

	const openModalOrNext = () => {
		["prestige", "reprise", "transformation", "mutation", "vente"].includes(serviceType.value)
			? goNext()
			: (modalIsOpen.value = true);
	};
</script>
