<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Alerte</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="intro-y flex items-center mt-8 lg:mt-16">
			<h2 class="text-lg font-semibold mr-auto">Vérifier un véhicule</h2>
		</div>

		<div class="flex justify-between items-end dashboard-card !rounded-md mt-4">
			<div class="w-full">
				<label class="form-label" for="crud-form-1">Entrer le numéro d'immatriculation de la voiture</label>
				<input id="crud-form-1" class="form-control w-full" placeholder="Input text" type="text" value="00XY" />
			</div>
			<div class="ms-2 w-1/5">
				<button class="btn btn-danger w-full h-auto" type="button" @click="modalIsOpen = true">Signaler</button>
			</div>
		</div>

		<div class="intro-y mt-8 flex items-center col-span-12">
			<h2 class="mr-auto text-lg font-bold">Liste des véhicules en alerte</h2>
		</div>
		<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
			<DataTable
				:headers="headers"
				:items="data"
				empty-text="Aucun produit d'épargne trouvé"
				header-class="uppercase text-start"
				search
			>
				<template #statut="{ item }">
					<StatusComponent :status="item.statut" />
				</template>
				<template #actions>
					<router-link class="text-primary" to="/police/verification/CarDetails">Voir détails</router-link>
				</template>
			</DataTable>
		</div>

		<Modal :show="modalIsOpen" size="modal-lg" @hidden="modalIsOpen = false">
			<ModalHeader class="pt-6">
				<h2 class="font-bold text-base mr-auto">Créer une alerte</h2>
				<button class="absolute right-0 top-0 mt-6 mr-3" @click="modalIsOpen = false">
					<i class="fa-solid fa-x w-8 h-4 font-bold" />
				</button>
			</ModalHeader>
			<ModalBody class="grid grid-cols-12 gap-4 gap-y-3">
				<div
					class="col-span-12 relative before:hidden before:lg:block before:absolute before:w-[40%] before:h-[3px] before:top-0 before:bottom-0 before:mt-4 before:bg-slate-100 before:dark:bg-darkmode-400 flex flex-col lg:flex-row justify-center px-5 sm:px-20"
				>
					<div
						v-for="(step, id, i) in stepper.steps.value"
						:key="id"
						class="intro-x lg:text-center flex items-center lg:block flex-1 z-10"
					>
						<button
							:disabled="stepper.isBefore(id)"
							class="w-10 h-10 rounded-full btn"
							:class="{
								'btn-primary': stepper.isCurrent(id),
								'btn-success': stepper.isAfter(id),
								'btn text-slate-500 bg-slate-100 dark:bg-darkmode-400 dark:border-darkmode-400':
									stepper.isBefore(id),
							}"
							@click="stepper.goTo(id)"
						>
							<template v-if="stepper.isAfter(id)">
								<i class="text-white fa-solid fa-check" />
							</template>
							<template v-else>
								{{ i + 1 }}
							</template>
						</button>
					</div>
				</div>
				<template v-if="stepper.isCurrent('vehicle-info')">
					<div class="intro-y col-span-12 mt-4">
						<label for="input-wizard-1" class="form-label">
							Enter le numéro d'immatriculation de la voiture
						</label>
						<input id="input-wizard-1" type="text" class="form-control" placeholder="00XY" />
					</div>

					<div class="intro-y flex col-span-12 items-center mt-4">
						<h4 class="text-base font-semibold mr-auto">Sélectionner le ou les motif(s)</h4>
					</div>

					<div class="col-span-12 grid grid-cols-12 gap-6 mt-5">
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
									<div class="flex flex-col lg:flex-row items-center p-2">
										<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
											<img
												alt="Cliquez pour choisir la demande d'immatriculation"
												class=""
												src="../../../assets/images/parc/Maintenance.png"
											/>
											<span
												class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
											>
												<i class="fa-light fa-check"></i>
											</span>
										</div>
										<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
											<h2 class="font-semibold">Visite technique</h2>
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
									value="3"
								/>
								<label
									class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
									for="custom-checkbox-3"
								>
									<div class="flex flex-col lg:flex-row items-center p-2">
										<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
											<img
												alt="Cliquez pour choisir la demande d'immatriculation"
												class=""
												src="../../../assets/images/parc/shield.png"
											/>
											<span
												class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
											>
												<i class="fa-light fa-check"></i>
											</span>
										</div>
										<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
											<h2 class="font-semibold">Assurance</h2>
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
									value="2"
								/>
								<label
									class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
									for="custom-checkbox-2"
								>
									<div class="flex flex-col lg:flex-row items-center p-2">
										<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
											<img
												alt="Cliquez pour choisir la demande d'immatriculation"
												class=""
												src="../../../assets/images/parc/hand_crossing_2.png"
											/>
											<span
												class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
											>
												<i class="fa-light fa-check"></i>
											</span>
										</div>
										<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
											<h2 class="font-semibold">Gage</h2>
										</div>
									</div>
								</label>
							</div>
						</div>
						<div class="intro-y col-span-12 md:col-span-6">
							<div class="relative group">
								<input
									id="custom-checkbox-8"
									v-model="serviceType"
									class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
									name="custom-checkbox"
									type="radio"
									value="8"
								/>
								<label
									class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
									for="custom-checkbox-8"
								>
									<div class="flex flex-col lg:flex-row items-center p-2">
										<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
											<img
												alt="Cliquez pour choisir la demande d'immatriculation"
												class=""
												src="../../../assets/images/parc/user_face_reversed.png"
											/>
											<span
												class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
											>
												<i class="fa-light fa-check"></i>
											</span>
										</div>
										<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
											<h2 class="font-semibold">Opposition</h2>
										</div>
									</div>
								</label>
							</div>
						</div>
						<div class="intro-y col-span-12 md:col-span-6">
							<div class="relative group">
								<input
									id="custom-checkbox-9"
									v-model="serviceType"
									class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
									name="custom-checkbox"
									type="radio"
									value="9"
								/>
								<label
									class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
									for="custom-checkbox-9"
								>
									<div class="flex flex-col lg:flex-row items-center p-2">
										<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
											<img
												alt="Cliquez pour choisir la demande d'immatriculation"
												class=""
												src="../../../assets/images/parc/apps.png"
											/>
											<span
												class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
											>
												<i class="fa-light fa-check"></i>
											</span>
										</div>
										<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
											<h2 class="font-semibold">Autre</h2>
										</div>
									</div>
								</label>
							</div>
						</div>
						<div class="intro-y col-span-12 md:col-span-6">
							<div class="relative group">
								<input
									id="custom-checkbox-12"
									v-model="serviceType"
									class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
									name="custom-checkbox"
									type="radio"
									value="12"
								/>
								<label
									class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
									for="custom-checkbox-12"
								>
									<div class="flex flex-col lg:flex-row items-center p-2">
										<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4">
											<img
												alt="Cliquez pour choisir la demande d'immatriculation"
												class=""
												src="../../../assets/images/parc/red_alert.png"
											/>
											<span
												class="hidden group-has-[input:checked]:flex floating-icon top-0 right-0 -mt-2 -mr-2.5"
											>
												<i class="fa-light fa-check"></i>
											</span>
										</div>
										<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
											<h2 class="font-semibold">Wanted</h2>
										</div>
									</div>
								</label>
							</div>
						</div>
					</div>

					<div class="intro-y col-span-12 mt-4">
						<label for="input-text-1" class="form-label"> Indication spéciale </label>
						<textarea
							id="input-text-1"
							type="text"
							class="form-control"
							rows="4"
							placeholder="Description"
						></textarea>
					</div>
					<div class="intro-y col-span-12 mt-4">
						<div class="flex mb-4">
							<button class="btn btn-danger w-full" type="button" @click="stepper.goTo('validation')">
								Signaler la voiture
							</button>
						</div>
					</div>
				</template>
				<template v-else-if="stepper.isCurrent('validation')">
					<div class="col-span-12 mt-8">
						<Alert class="alert-outline-danger border-2 bg-dark-50 flex items-center mb-2 font-bold">
							<i class="fa-solid text-xl text-danger fa-exclamation-triangle w-6 h-6 mr-4" />
							<span class="text-dark">
								Assurez-vous que le informations concernant le véhicule sont exactes.
							</span>
						</Alert>
					</div>
					<div class="col-span-12">
						<p class="text-base mb-2 font-bold">Immatricule :</p>
						<ul class="px-8">
							<li class="list-disc text-base">00XY</li>
						</ul>

						<p class="text-base mb-2 mt-4 font-bold">Motif(s) :</p>
						<ul class="px-8">
							<li class="list-disc text-base">Gage</li>
							<li class="list-disc text-base">Visite technique</li>
						</ul>
						<p class="text-base mb-2 mt-4 font-bold">Indication spéciale :</p>
						<p class="text-base mb-2">---------------------------------------</p>
					</div>
					<div class="intro-y col-span-12 mt-4">
						<div class="flex mb-4">
							<button class="btn btn-danger w-full" type="button" @click="openSuccessModal">
								Confirmer
							</button>
						</div>
					</div>
				</template>
			</ModalBody>
		</Modal>

		<Modal :show="successModalIsOpen" backdrop="static" @hidden="successModalIsOpen = false">
			<ModalBody class="p-0">
				<div class="p-5 text-center font-bold">
					<svg
						class="lucide lucide-check-circle w-16 h-16 text-success mx-auto mt-3"
						fill="none"
						height="24"
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						viewBox="0 0 24 24"
						width="24"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
					<div class="text-2xl mt-5">Le véhicule a été signalé avec succès</div>
				</div>
				<div class="px-5 pb-8 text-center mt-4">
					<button class="btn btn-danger w-full mx-2" type="button" @click="successModalIsOpen = false">
						Ok j'ai compris !
					</button>
				</div>
			</ModalBody>
		</Modal>
	</div>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import NotificationBox from "@/components/NotificationBox.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { ref } from "vue";
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import { useStepper } from "@vueuse/core";
	import Alert from "@/global-components/alert/index.js";

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Immatriculation", sortable: false },
		{ key: "vin", title: "Véhicule", sortable: false },
		{ key: "owner_name", title: "Conducteur", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Passage", sortable: false },
	];

	const data = ref([
		{
			key: 1,
			reference: "REF-0001",
			bank: "BANK-0001",
			owner_name: "John Doe",
			vin: "VIN-0001",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 2,
			reference: "REF-0002",
			bank: "BANK-0002",
			owner_name: "Jane Doe",
			vin: "VIN-0002",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 3,
			reference: "REF-0003",
			bank: "BANK-0003",
			owner_name: "John Doe",
			vin: "VIN-0003",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
		{
			key: 4,
			reference: "REF-0004",
			bank: "BANK-0004",
			owner_name: "Jane Doe",
			vin: "VIN-0004",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 5,
			reference: "REF-0005",
			bank: "BANK-0005",
			owner_name: "John Doe",
			vin: "VIN-0005",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 6,
			reference: "REF-0006",
			bank: "BANK-0006",
			owner_name: "Jane Doe",
			vin: "VIN-0006",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
		{
			key: 7,
			reference: "REF-0007",
			bank: "BANK-0007",
			owner_name: "John Doe",
			vin: "VIN-0007",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
		{
			key: 8,
			reference: "REF-0008",
			bank: "BANK-0008",
			owner_name: "Jane Doe",
			vin: "VIN-0008",
			created_at: "2021-10-10 10:00:00",
			statut: "pending",
		},
		{
			key: 9,
			reference: "REF-0009",
			bank: "BANK-0009",
			owner_name: "John Doe",
			vin: "VIN-0009",
			created_at: "2021-10-10 10:00:00",
			statut: "rejected",
		},
		{
			key: 10,
			reference: "REF-0010",
			bank: "BANK-0010",
			owner_name: "Jane Doe",
			vin: "VIN-0010",
			created_at: "2021-10-10 10:00:00",
			statut: "validated",
		},
	]);

	const modalIsOpen = ref(false);
	const successModalIsOpen = ref(false);
	const serviceType = ref("demand");

	const openSuccessModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = true;
	};

	const stepper = useStepper({
		"vehicle-info": {
			title: "Informations du véhicule",
			isValid: () => true,
		},
		validation: {
			title: "Validation",
			isValid: () => true,
		},
	});
</script>

<style scoped></style>
