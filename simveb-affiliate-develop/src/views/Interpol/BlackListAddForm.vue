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
				<div v-if="formType === 'add'" class="my-4">
					<TextInputGroup
						v-model="vehicle.vin"
						name="vin"
						label="Entrer le VIN du véhicule"
						:disabled="formLoading"
						:errors="errors.vin || []"
						required
					/>
				</div>
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
						<a
							:href="API_URL.replace(/\/$/, '') + '/black-vehicles/file-format'"
							class="font-bold underline text-primary"
							download
						>
							Cliquez ici
						</a>
						pour télécharger le modèle d'importation
					</div>
				</template>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton class="btn-primary w-full" type="submit" :loading="formLoading"> Enregistrer </BasicButton>
			</div>
		</ModalFooter>
	</Modal>

	<SuccessModalComponent
		:open="successModalOpen"
		:message="
			formType === 'add'
				? 'Le véhicule a bien été ajoutée. En attente de validation'
				: 'Les véhicules ont été importés avec succès. En attente de validation'
		"
		@close="successModalOpen = false"
	/>
</template>

<script setup>
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { onUnmounted, ref, watch } from "vue";
	import { storeToRefs } from "pinia";
	import FileInputGroup from "@/components/Form/FileInputGroup.vue";
	import SuccessModalComponent from "@/components/SuccessModalComponent.vue";
	import { useCrudStore } from "@/stores/crud.js";

	const props = defineProps({
		open: Boolean,
		multiple: Boolean,
		update: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(["close"]);
	const API_URL = import.meta.env.VITE_API_URL;
	const formType = ref("add");
	const successModalOpen = ref(false);

	const crudStore = useCrudStore();
	const { row: vehicle, formLoading, errors, url } = storeToRefs(useCrudStore());

	const submit = () => {
		url.value = "blacklist-vehicles";
		if (formType.value === "add") {
			crudStore.createRow(vehicle.value).then(() => {
				emit("close");
				successModalOpen.value = true;
			});
		} else {
			crudStore.createWithFile(vehicle.value).then(() => {
				emit("close");
				successModalOpen.value = true;
			});
		}
	};

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
