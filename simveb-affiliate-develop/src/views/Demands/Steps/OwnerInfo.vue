<template>
	<LoaderSpinner v-if="loading || !ownerInfo" type="block" class="mt-4" />

	<template v-else>
		<div class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
			<OwnerInfoCard :owner-info="ownerInfo" />
		</div>
		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Annuler
				</button>
				<BasicButton class="btn-primary w-36" type="button" :loading="formLoading" @click="otpModalOpen = true">
					Suivant
				</BasicButton>
			</div>
		</div>
	</template>

	<Modal :show="modalIsOpen" backdrop="static" is-form @hidden="modalIsOpen = false" @submit="submit">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div>
					<TextInputGroup
						v-model="npi"
						:name="demand.userType === 'physique' ? 'npi' : 'ifu'"
						:label="
							demand.userType === 'physique'
								? 'Entrer le NPI de l\'acheteur'
								: 'Entrer l\'IFU de l\'entreprise'
						"
						:disabled="loading"
						:pattern="demand.userType === 'physique' ? '[0-9]{10}' : '[0-9]{13}'"
						:maxlength="demand.userType === 'physique' ? 10 : 13"
						:errors="errors.npi || []"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<button class="btn btn-outline-secondary w-full mr-4 border-2" type="reset" @click="goBack">
					Annuler
				</button>
				<BasicButton class="btn-primary w-full" type="submit" :loading="loading"> Suivant </BasicButton>
			</div>
		</ModalFooter>
	</Modal>

	<OtpModalForm
		:npi="npi"
		:is-company="demand.userType === 'company'"
		:open="otpModalOpen"
		@submit="(authorization_id) => goNext(authorization_id)"
		@close="otpModalOpen = false"
		@loading="formLoading = true"
		@loaded="formLoading = false"
	/>
</template>

<script setup>
	import { onBeforeUnmount, onMounted, ref } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import OtpModalForm from "@/views/OtpModalForm.vue";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, loading, owner_info: ownerInfo, errors, formLoading } = storeToRefs(demandStore);
	const npi = ref("");

	const modalIsOpen = ref(false);
	const otpModalOpen = ref(false);

	const submit = () => {
		demandStore.fetchOwnerInfo(npi.value, demand.value.userType === "company").then((res) => {
			ownerInfo.value = res;
			demand.value.userType === "company" ? (demand.value.ifu = npi.value) : (demand.value.npi = npi.value);
			modalIsOpen.value = false;
		});
	};

	const goNext = (authorization_id) => {
		otpModalOpen.value = false;
		demand.value.authorization_id = authorization_id;
		emit("next");
	};

	const goBack = () => {
		modalIsOpen.value = false;
		emit("prev");
	};

	onMounted(() => {
		if (!ownerInfo.value) {
			modalIsOpen.value = true;
		}
	});

	onBeforeUnmount(() => {
		modalIsOpen.value = false;
		otpModalOpen.value = false;
	});
</script>
