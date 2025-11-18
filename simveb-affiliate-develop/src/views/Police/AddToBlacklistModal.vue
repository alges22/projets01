<template>
	<button v-if="vehicleProvenance === 'local'" class="btn btn-outline-violet" type="button" @click="addToBlackList">
		Ajouter à la liste noire
	</button>
	<button
		v-if="vehicleProvenance === 'external'"
		class="btn btn-outline-violet"
		type="button"
		@click="blackListModalIsOpen = true"
	>
		Ajouter à la liste noire
	</button>

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
	const props = defineProps({
		vehicleProvenance: { type: String, default: "" },
	});

	import { ref } from "vue";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import { storeToRefs } from "pinia";
	import AlertNotification from "@/components/notification/alert.js";
	import { useVehiclePassageStore } from "../../stores/passage.js";
	import { useAlertStore } from "@/stores/alert";

	const alertStore = useAlertStore();
	const { alert } = storeToRefs(alertStore);

	const vehiclePassageStore = useVehiclePassageStore();
	const { passage } = storeToRefs(vehiclePassageStore);

	const blackListModalIsOpen = ref(false);
	const ownerFirstname = ref("");
	const ownerLastname = ref("");

	const addToBlackList = () => {
		let data = {
			vehicle_type: props.vehicleProvenance,
		};

		if (props.vehicleProvenance === "local") {
			if (passage.value.vehicle) {
				data.vehicle_id = passage.value.vehicle.id;
			}
			if (alert.value.vehicle) {
				data.vehicle_id = alert.value.vehicle.id;
			}
		} else if (props.vehicleProvenance === "external") {
			data.foreign_vehicle_immatriculation_number = passage.value.vehicle.immatriculation_number;
			data.owner_firstname = ownerFirstname.value;
			data.owner_lastname = ownerLastname.value;
		}
		vehiclePassageStore.addToBlackList(data).then((response) => {
			AlertNotification.success("Véhicule ajouté à la liste noire");
			blackListModalIsOpen.value = false;
		});
	};
</script>
