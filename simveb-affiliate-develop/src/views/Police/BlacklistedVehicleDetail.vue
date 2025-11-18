<template>
	<div class="content">
		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button
					v-if="vehicle?.vehicle_provenance === 'local'"
					class="btn btn-outline-danger mr-4 border-2"
					type="button"
					@click="alertTypeModalIsOpen = true"
				>
					Signaler la voiture
				</button>
				<button
					v-if="vehicle?.vehicle_provenance === 'local'"
					class="btn btn-outline-violet"
					type="button"
					@click="addToBlackList"
				>
					Ajouter à la liste noire
				</button>
				<button
					v-if="vehicle?.vehicle_provenance === 'external'"
					class="btn btn-outline-violet"
					type="button"
					@click="blackListModalIsOpen = true"
				>
					Ajouter à la liste noire
				</button>
			</div>
		</div>

		<div class="intro-y box mt-8 rounded-lg">
			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<div v-if="vehicle?.vehicle">
					<DetailVehicleInfoCard :vehicle-info="vehicle?.vehicle" />
				</div>
			</div>

			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<div v-if="vehicle?.vehicle_owner">
					<DetailOwnerInfoCard :owner-info="vehicle?.vehicle_owner" />
				</div>
			</div>

			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<div v-if="vehicle?.officer">
					<DetailOfficerInfoCard :officer-info="vehicle?.officer" />
				</div>
			</div>

			<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
				<div v-if="vehicle?.vehicle">
					<PassageHistory :id="vehicle?.vehicle?.immatriculation_number" />
				</div>
			</div>
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-primary mr-4 border-2 w-48" type="button">Imprimer</button>
			</div>
		</div>
	</div>

	<AlertVehicleModal
		v-if="vehicle?.vehicle"
		:alert-type-modal-is-open="alertTypeModalIsOpen"
		:registration-number="vehicle?.vehicle?.immatriculation_number"
		@close-alert-modal="alertTypeModalIsOpen = false"
	></AlertVehicleModal>

	<Modal :show="blackListModalIsOpen" size="modal-xl" @hidden="blackListModalIsOpen = false">
		<ModalHeader class="pt-6">
			<h2 class="font-bold text-base mr-auto">Ajouter le véhicule à la liste noire</h2>
			<button class="absolute right-0 top-0 mt-6 mr-3" @click="blackListModalIsOpen = false">
				<i class="fa-solid fa-x w-8 h-4 font-bold" />
			</button>
		</ModalHeader>
		<ModalBody>
			<div class="grid grid-cols-2">
				<div class="flex flex-col justify-between mx-4">
					<div>
						<span class="form-label" for="crud-form-1">Nom du propriétaire</span>
						<input
							id="crud-form-1"
							v-model="ownerLastname"
							class="form-control w-full"
							placeholder="Nom du propriétaire"
							type="text"
						/>
					</div>
				</div>

				<div class="flex flex-col justify-between mx-4">
					<div>
						<span class="form-label" for="crud-form-1">Prénom du propriétaire</span>
						<input
							id="crud-form-1"
							v-model="ownerFirstname"
							class="form-control w-full"
							placeholder="Prénom du propriétaire"
							type="text"
						/>
					</div>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="mb-2 text-right">
				<button class="btn bg-red-600 text-white w-48 mr-4" type="button" @click="addToBlackList">
					Ajouter
				</button>

				<button class="btn btn-primary border-2 w-48" type="button" @click="blackListModalIsOpen = false">
					Annuler
				</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { onMounted, ref } from "vue";
	import { useRoute } from "vue-router";
	import { storeToRefs } from "pinia";
	import DetailVehicleInfoCard from "@/views/Police/shared/DetailVehicleInfoCard.vue";
	import DetailOwnerInfoCard from "@/views/Police/shared/DetailOwnerInfoCard.vue";
	import DetailOfficerInfoCard from "@/views/Police/shared/DetailOfficerInfoCard.vue";
	import PassageHistory from "@/views/Police/shared/PassageHistory.vue";
	import { useVehiclePassageStore } from "../../stores/passage.js";
	import Alert from "@/components/notification/alert.js";
	import { useAlertStore } from "@/stores/alert";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import AlertVehicleModal from "@/views/Police/Alerts/AlertVehicleModal.vue";
	import { useBlacklistedVehicleStore } from "@/stores/blacklisted-vehicle";

	const alertStore = useAlertStore();
	const vehiclePassageStore = useVehiclePassageStore();
	const { passage } = storeToRefs(vehiclePassageStore);

	const blacklistedVehicleStore = useBlacklistedVehicleStore();
	const { vehicle } = storeToRefs(useBlacklistedVehicleStore);

	const route = useRoute();
	const id = route.params.id;

	const alertTypeModalIsOpen = ref(false);
	const blackListModalIsOpen = ref(false);
	const ownerFirstname = ref("");
	const ownerLastname = ref("");

	const addToBlackList = () => {
		let data = {
			vehicle_type: vehicle.value.vehicle_provenance,
		};

		if (vehicle.value.vehicle_provenance === "local") {
			data.vehicle_id = vehicle.value.vehicle.vehicle_id;
		} else if (vehicle.value.vehicle_provenance === "external") {
			data.foreign_vehicle_immatriculation_number = vehicle.value.vehicle.immatriculation_number;
			data.owner_firstname = ownerFirstname.value;
			data.owner_lastname = ownerLastname.value;
		}
		vehiclePassageStore.addToBlackList(data).then((response) => {
			Alert.success("Véhicule ajouté à la liste noire");
			blackListModalIsOpen.value = false;
		});
	};

	onMounted(() => {
		blacklistedVehicleStore.fetchBlacklistedVehicleDetail({ id: id });
	});
</script>

<style scoped></style>
