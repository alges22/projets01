<template>
	<Modal is-form :show="modalIsOpen" @hidden="$emit('close')" @submit="submit">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div v-if="formDataLoaded" class="mt-4">
					<SelectInputGroup
						v-model="row.position_id"
						:options="formData.positions"
						name="position_id"
						label="Choisir une position"
						:disabled="formLoading"
						:errors="errors.position_id || []"
						option-text="name"
						option-value="id"
					/>
				</div>
				<div v-if="formDataLoaded" class="mt-4">
					<SelectMultipleGroup
						v-model="row.roles"
						:options="formData.roles"
						name="roles"
						label="Définissez des rôles"
						:disabled="formLoading"
						:errors="errors.roles || []"
						option-text="label"
						option-value="name"
						@update:add="(value) => (row.roles ? row.roles.push(value) : (row.roles = [value]))"
						@update:remove="(value) => (row.roles = row.roles.splice(row.roles.indexOf(value), 1))"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button type="reset" class="btn-secondary w-full me-2" @click="$emit('close')">Annuler</button>
				<BasicButton :loading="formLoading" class="btn-primary w-full" type="submit">Affecter</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { ref, watch } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import BasicButton from "@/components/BasicButton.vue";
	import SelectMultipleGroup from "@/components/Form/SelectMultipleGroup.vue";
	import Alert from "@/components/notification/alert.js";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";

	const emit = defineEmits(["submit", "close"]);

	const props = defineProps({
		open: Boolean,
		npi: String,
	});

	const crudStore = useCrudStore();
	const { row, formData, formDataLoaded, url, errors, formLoading } = storeToRefs(crudStore);
	const modalIsOpen = ref(false);

	const submit = async () => {
		if (
			await crudStore.createRow({
				...row.value,
				npi: props.npi,
			})
		) {
			Alert.success("Affectation réussie");
			modalIsOpen.value = false;
			row.value = {};
			emit("submit");
		}
	};

	watch(
		() => props.open,
		(value) => {
			if (value) {
				url.value = "/police/officers";
				!formDataLoaded.value && crudStore.loadCreateData();
				modalIsOpen.value = true;
			} else {
				modalIsOpen.value = false;
				row.value = {};
			}
		}
	);
</script>

<style scoped></style>
