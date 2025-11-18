<template>
	<Modal is-form :show="modalIsOpen" @hidden="$emit('close')" @submit="sendInvitation">
		<ModalBody>
			<LoaderSpinner v-if="loading" />
			<div v-else class="flex flex-col justify-between mx-4">
				<div>
					<TextInputGroup
						v-model="staff.npi"
						required
						name="npi"
						placeholder="XXXXXXXXXX"
						label="Entrer le NPI du personnel"
						:disabled="formLoading"
						:pattern="'[0-9]{10}'"
						:maxlength="10"
						:errors="errors.npi || []"
						class="w-full"
					/>
				</div>
				<div v-if="crudData" class="mt-4">
					<SelectMultipleGroup
						v-model="staff.roles"
						:options="crudData.roles"
						name="roles"
						label="Définissez des rôles"
						:disabled="formLoading"
						:errors="errors.roles || []"
						option-text="label"
						option-value="id"
						@update:add="(value) => (staff.roles ? staff.roles.push(value) : (staff.roles = [value]))"
						@update:remove="(value) => (staff.roles = staff.roles.splice(staff.roles.indexOf(value), 1))"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<BasicButton :loading="formLoading" class="btn-primary w-full" type="submit">Inviter</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>
<script setup>
	import { useStaffStore } from "@/stores/staff.js";
	import { storeToRefs } from "pinia";
	import { ref, watch } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import BasicButton from "@/components/BasicButton.vue";
	import SelectMultipleGroup from "@/components/Form/SelectMultipleGroup.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import Alert from "@/components/notification/alert.js";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";

	const emit = defineEmits(["submit", "close"]);

	const props = defineProps({
		open: Boolean,
	});

	const staffStore = useStaffStore();
	const { staff, formData: crudData, formDataLoaded: loaded, errors, formLoading, loading } = storeToRefs(staffStore);
	const modalIsOpen = ref(false);

	const sendInvitation = () => {
		staffStore.sendInvitation(staff.value).then(() => {
			Alert.success("Invitation envoyée avec succès");
			modalIsOpen.value = false;
			staff.value = {};
			emit("close");
		});
	};

	watch(
		() => props.open,
		(value) => {
			if (value) {
				!loaded.value && staffStore.loadCreateData();
				modalIsOpen.value = true;
			} else {
				modalIsOpen.value = false;
				staff.value = {};
			}
		}
	);
</script>
