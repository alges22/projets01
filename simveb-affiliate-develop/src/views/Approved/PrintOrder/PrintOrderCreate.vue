<template>
	<LoaderSpinner v-if="loading || !order" type="block" class="mt-4" />

	<template v-else>
		<div class="intro-y flex items-center mt-4">
			<h2 class="text-lg font-semibold m-auto">Information de l'immatriculation</h2>
		</div>

		<div v-if="order" class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary text-2xl">Service</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Type de service</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.demand.service.name }}</p>
					</div>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<div class="flex items-center pb-2 mt-2 border-b">
						<p class="w-1/2 text-lg leading-5">Référence d'impression</p>
						<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ order.reference }}</p>
					</div>
				</div>
			</div>

			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary text-2xl">Informations d'immatriculation</h2>
				</div>
			</div>
			<div v-if="immatriculation" class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<p class="text-lg leading-5 mb-2 text-center">Avant</p>
					<ImmatriculationPlateView
						class="mx-auto"
						:bg-color="immatriculation.plate_color.color_code"
						:text-color="immatriculation.plate_color.text_color"
						:form="immatriculation.front_plate_shape.code"
						:immatriculation-number="immatriculation.label ?? immatriculation.number"
						:is-label="!!immatriculation.label"
						:country-code="immatriculation.country_code"
					/>
				</div>
				<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
					<p class="text-lg leading-5 mb-2 text-center">Arrière</p>
					<ImmatriculationPlateView
						class="mx-auto"
						:bg-color="immatriculation.plate_color.color_code"
						:text-color="immatriculation.plate_color.text_color"
						:form="immatriculation.back_plate_shape.code"
						:immatriculation-number="immatriculation.label ?? immatriculation.number"
						:is-label="!!immatriculation.label"
						:country-code="immatriculation.country_code"
					/>
				</div>
			</div>

			<OwnerInfoCard v-if="owner" :owner-info="owner.identity" />

			<VehicleInfoCard v-if="vehicle" :vehicle-info="vehicle" />
		</div>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.back()">
					Annuler
				</button>
				<BasicButton
					class="btn-primary w-36"
					type="button"
					:loading="formLoading || otpLoading"
					@click="otpModalOpen = true"
				>
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
						v-model="reference"
						name="reference"
						required
						label="Entrer la référence de la demande d'impression"
						:disabled="formLoading"
						:errors="errors.reference || []"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton
					class="btn-secondary w-full me-2"
					type="reset"
					:disabled="formLoading"
					@click="$router.back()"
				>
					<i class="fa-light fa-arrow-left me-2"></i>
					Retour
				</BasicButton>
				<BasicButton class="btn-primary w-full" type="submit" :loading="formLoading"> Suivant </BasicButton>
			</div>
		</ModalFooter>
	</Modal>

	<OtpModalForm
		v-if="owner"
		:npi="owner.identity.npi"
		:is-company="!!owner.identity.ifu"
		:open="otpModalOpen"
		@submit="(authorization_id) => goNext(authorization_id)"
		@close="otpModalOpen = false"
		@loading="otpLoading = true"
		@loaded="otpLoading = false"
	/>
</template>

<script setup>
	import { onBeforeMount, onMounted, ref } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import OtpModalForm from "@/views/OtpModalForm.vue";
	import { onBeforeRouteLeave, useRouter } from "vue-router";
	import { usePrintOrderStore } from "@/stores/print-order.js";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import client from "@/assets/js/axios/client.js";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import Alert from "@/components/notification/alert.js";
	import ImmatriculationPlateView from "@/components/immatriculation/ImmatriculationPlateView.vue";

	const router = useRouter();
	const orderStore = usePrintOrderStore();
	const { order, loading, errors, formLoading } = storeToRefs(orderStore);
	const reference = ref("");
	const owner = ref(null);
	const vehicle = ref(null);
	const immatriculation = ref(null);
	const otpLoading = ref(false);

	const modalIsOpen = ref(false);
	const otpModalOpen = ref(false);

	const submit = () => {
		orderStore.searchOrder(reference.value).then((res) => {
			modalIsOpen.value = false;
			owner.value = res.immatriculation.vehicle.owner;
			immatriculation.value = res.immatriculation;
			vehicle.value = res.immatriculation.vehicle;
		});
	};

	const goNext = async (authorization_id) => {
		otpModalOpen.value = false;
		formLoading.value = true;
		await client({
			method: "POST",
			url: "print-orders/confirm-affectation",
			data: {
				print_order_id: order.value.id,
				authorization_id: authorization_id,
			},
		})
			.then((res) => {
				Alert.success(res.data.message);
				router.push({ name: "print-orders-confirm", params: { id: order.value.id } });
			})
			.finally(() => {
				formLoading.value = false;
			});
	};

	onMounted(() => {
		modalIsOpen.value = true;
	});

	onBeforeMount(() => {
		order.value = null;
	});

	onBeforeRouteLeave(() => {
		orderStore.clear();
		modalIsOpen.value = false;
		otpModalOpen.value = false;
	});
</script>
