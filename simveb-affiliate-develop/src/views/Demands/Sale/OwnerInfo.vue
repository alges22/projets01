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
				<button class="btn btn-primary w-36" type="button" @click="$emit('next')">Suivant</button>
			</div>
		</div>
	</template>

	<Modal :show="modalIsOpen" backdrop="static" is-form @hidden="modalIsOpen = false" @submit="submit">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<span class="text-xl font-bold text-center mb-4">{{ title }}</span>
				<div class="grid grid-cols-2 gap-6 rounded-2xl">
					<label
						class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
						for="seller_type_moral"
					>
						<span class="form-check ml-auto ms-4">
							<input
								id="seller_type_moral"
								v-model="sellerType"
								class="form-check-input"
								name="seller-type"
								type="radio"
								value="physique"
							/>
						</span>
						<span class="w-full mx-4"> NPI </span>
					</label>
					<label
						class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
						for="seller_type_physique"
					>
						<span class="form-check ml-auto ms-4">
							<input
								id="seller_type_physique"
								v-model="sellerType"
								class="form-check-input"
								name="seller-type"
								type="radio"
								value="moral"
							/>
						</span>
						<span class="w-full mx-4"> IFU </span>
					</label>
				</div>
				<div class="mt-4">
					<TextInputGroup
						v-model="demand.npi"
						:name="sellerType === 'physique' ? 'npi' : 'ifu'"
						:disabled="loading"
						required
						:placeholder="sellerType === 'physique' ? 'Entrer le NPI' : 'Entrer l\'IFU'"
						:pattern="sellerType === 'physique' ? '[0-9]{10}' : '[0-9]{13}'"
						:maxlength="sellerType === 'physique' ? 10 : 13"
						:errors="errors.npi || []"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton class="btn-outline-secondary w-full me-2" type="reset" @click="$router.back()">
					Annuler
				</BasicButton>
				<BasicButton class="btn-primary w-full" type="submit" :loading="loading"> Suivant </BasicButton>
			</div>
		</ModalFooter>
	</Modal>
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
	import { onBeforeRouteLeave } from "vue-router";

	defineEmits(["next", "prev"]);

	defineProps({
		title: {
			required: false,
			type: String,
			default: "Vendeur",
		},
	});

	const demandStore = useDemandStore();
	const { demand, loading, owner_info: ownerInfo, errors } = storeToRefs(demandStore);
	const sellerType = ref("physique");

	const modalIsOpen = ref(false);

	const submit = () => {
		demandStore.fetchOwnerInfo(demand.value.npi, sellerType.value === "company").then((res) => {
			ownerInfo.value = res;
			modalIsOpen.value = false;
		});
	};

	onMounted(() => {
		if (!ownerInfo.value) {
			modalIsOpen.value = true;
		}
	});

	onBeforeUnmount(() => {
		modalIsOpen.value = false;
	});

	onBeforeRouteLeave(() => {
		modalIsOpen.value = false;
	});
</script>
