<template>
	<Modal size="modal-lg" is-form :show="open" @submit="submit" @hidden="$emit('close')">
		<ModalHeader>
			<ul v-if="!update" class="nav-tabs flex justify-center items-end !border-none w-full">
				<li
					v-for="(form, index) in [
						{
							type: 'add',
							label: 'Un véhicule',
						},
						{
							type: 'import',
							label: 'Plusieurs véhicules',
						},
					]"
					:key="index"
					class="w-1/2 font-bold text-lg nav-item"
					:class="formType === form.type ? 'border-b-4 border-primary' : ''"
					@click="formType = form.type"
					@keydown.enter="formType = form.type"
				>
					<span
						class="nav-link text-center cursor-pointer"
						:class="formType === form.type ? 'text-primary' : 'opacity-75 text-gray-400'"
					>
						{{ form.label }}
					</span>
				</li>
			</ul>
		</ModalHeader>
		<ModalBody>
			<div class="flex flex-col justify-between">
				<template v-if="formType === 'add'">
					<div class="my-4">
						<TextInputGroup
							v-model="vehicle.vin"
							name="vin"
							label="Entrer le VIN du véhicule"
							:disabled="formLoading"
							:errors="errors.vin || []"
							required
						/>
					</div>
					<div v-if="formDataLoaded" class="my-4">
						<SelectInputGroup
							v-model="vehicle.institution_id"
							:options="formData.institutions || []"
							name="institution_id"
							required
							label="Choisir l'institution"
							:disabled="formLoading"
							:errors="errors.institution_id || []"
						/>
					</div>
					<div class="my-4">
						<TextInputGroup
							v-model="vehicle.customs_reference"
							name="customs_reference"
							label="Entrer le numéro de la quittance de douane"
							placeholder="VN1324718265422NVV"
							add-class="w-full"
							:disabled="formLoading"
							:errors="errors.customs_reference || []"
						/>
					</div>
				</template>
				<template v-else>
					<FileInputGroup
						v-model="vehicle.file"
						name="vehicle-import-file"
						label="Charger le fichier Excel"
						required
						:disabled="formLoading"
						accept=".xls,.xlsx"
						:errors="errors.documents || []"
					>
						<p class="mt-1 text-xs leading-4 text-gray-600">
							Format pris en compte: xls, xlsx - Taille : 500 Ko
						</p>
					</FileInputGroup>
					<div class="mt-4">
						<a href="/imports/gma_vehicle_template.xlsx" class="font-bold underline text-primary" download>
							Cliquez ici
						</a>
						pour télécharger le modèle d'importation
					</div>
				</template>
				<div class="my-4">
					<FileInputGroup
						v-model="vehicle.declaration_file"
						name="declaration_file"
						label="Déclaration"
						:required="!update"
						:disabled="formLoading"
						accept="application/pdf,image/*"
						:errors="errors.declaration_file || []"
					>
						<p class="mt-1 text-xs leading-4 text-gray-600">
							Format pris en compte: pdf, image - Taille : 2 Mo
						</p>
					</FileInputGroup>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton v-if="update" class="btn-secondary w-full me-2" type="reset" @click="emit('close')">
					Annuler
				</BasicButton>
				<BasicButton class="btn-primary w-full me-2" type="submit" :loading="formLoading">
					{{ update ? "Mettre à jour" : "Enregistrer" }}
				</BasicButton>
			</div>
		</ModalFooter>
	</Modal>

	<SuccessModalComponent
		:open="successModalOpen"
		:message="
			formType === 'add'
				? 'Les informations du véhicule ont été enregistrées avec succès'
				: 'Les véhicules ont été importés avec succès'
		"
		@close="successModalOpen = false"
	/>
</template>

<script setup>
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { useVehicleStore } from "@/stores/vehicle.js";
	import { onMounted, onUnmounted, ref, watch } from "vue";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
	import { storeToRefs } from "pinia";
	import FileInputGroup from "@/components/Form/FileInputGroup.vue";
	import SuccessModalComponent from "@/components/SuccessModalComponent.vue";
	import Alert from "@/components/notification/alert.js";

	const props = defineProps({
		open: Boolean,
		multiple: Boolean,
		url: {
			type: String,
			required: true,
		},
		update: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(["close", "submit"]);
	const formType = ref("add");
	const successModalOpen = ref(false);

	const vehicleStore = useVehicleStore();
	const { vehicle, formLoading, formData, formDataLoaded, errors, url: formURL } = storeToRefs(useVehicleStore());

	const submit = () => {
		if (formType.value === "add") {
			if (props.update) {
				vehicleStore
					.updateVehicle(
						vehicle.value.id,
						{
							vin: vehicle.value.vin,
							institution_id: vehicle.value.institution_id,
							customs_reference: vehicle.value.customs_reference,
							declaration_file: vehicle.value.declaration_file,
						},
						true
					)
					.then(() => {
						emit("submit");
						Alert.success("Véhicule mis à jour avec succès.");
					});
			} else {
				vehicleStore.createVehicle(vehicle.value, false, true).then(() => {
					emit("submit");
					successModalOpen.value = true;
				});
			}
		} else {
			vehicleStore.createVehicle(vehicle.value, true).then(() => {
				emit("submit");
				successModalOpen.value = true;
			});
		}
	};

	onMounted(async () => {
		formURL.value = props.url;
		await vehicleStore.loadCreateData();
	});

	onUnmounted(() => {
		vehicle.value = {};
	});

	watch(
		() => props.open,
		(value) => {
			if (value) {
				formType.value = props.multiple ? "import" : "add";
			}
		}
	);
</script>

<style scoped></style>
