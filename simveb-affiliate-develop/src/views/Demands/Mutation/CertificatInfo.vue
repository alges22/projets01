<template>
	<div v-if="certificate" class="justify-center flex mt-4">
		<div class="dashboard-card md:w-2/3 w-full mx-auto">
			<div class="p-5 text-center">
				<svg
					class="lucide lucide-check-circle w-24 h-24 text-success mx-auto mt-3"
					fill="none"
					height="32"
					stroke="currentColor"
					stroke-linecap="round"
					stroke-linejoin="round"
					stroke-width="2"
					viewBox="0 0 24 24"
					width="32"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
					<polyline points="22 4 12 14.01 9 11.01"></polyline>
				</svg>
				<div class="text-2xl mt-5 font-bold text-primary">
					Certificat de cession confirmé !
					<div class="w-1/3 bg-gray-400 h-px mt-2 m-auto"></div>
				</div>
			</div>

			<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
				<p class="col-span-12 w-full text-lg leading-5">
					{{
						`Ce véhicule a été vendu par M. ${certificate.owner.firstname} ${certificate.owner.lastname}
						à Mr/Mme ${certificate.buyer.firstname} ${certificate.buyer.lastname}`
					}}
				</p>
				<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
					<div class="intro-y flex items-center">
						<h2 class="text-primary text-2xl">Vendeur</h2>
					</div>
				</div>
				<div class="col-span-12 grid grid-cols-12 gap-6">
					<div class="col-span-12 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Nom & Prénom</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ `${certificate.owner.firstname} ${certificate.owner.lastname}` }}
							</p>
						</div>
					</div>
				</div>

				<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
					<div class="intro-y flex items-center">
						<h2 class="text-primary text-2xl">Acheteur</h2>
					</div>
				</div>
				<div class="col-span-12 grid grid-cols-12 gap-6">
					<div class="col-span-12 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Nom & Prénom</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ `${certificate.buyer.firstname} ${certificate.buyer.lastname}` }}
							</p>
						</div>
					</div>
				</div>

				<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
					<div class="intro-y flex items-center">
						<h2 class="text-primary text-2xl">Véhicule</h2>
					</div>
				</div>
				<div class="col-span-12 grid grid-cols-12 gap-6">
					<div class="col-span-12 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-6 border-b">
							<p class="w-1/2 text-lg leading-5">Marque</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicle_info.vehicle_brand }}</p>
						</div>
						<div class="flex items-center pb-2 mt-6 border-b">
							<p class="w-1/2 text-lg leading-5">Modèle de véhicule</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ vehicle_info.vehicle_model }}
							</p>
						</div>
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Immatriculation</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">
								{{ vehicle_info.immatriculation }}
							</p>
						</div>
						<div class="flex items-center pb-2 mt-6 border-b">
							<p class="w-1/2 text-lg leading-5">Numéro du châssis</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ vehicle_info.vin }}</p>
						</div>
					</div>
				</div>
			</div>

			<div class="p-5 text-center mt-12">
				<button class="btn btn-primary w-full" type="button" @click="$emit('next')">Suivant</button>
			</div>
		</div>
	</div>

	<Modal :show="askModalIsOpen" backdrop="static" is-form @hidden="askModalIsOpen = false" @submit="load">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div>
					<TextInputGroup
						v-model="demand.sale_declaration_reference"
						name="sale_declaration_reference"
						label="Entrer le numéro du certificat de cession"
						required
						:errors="errors.sale_declaration_reference"
						:disabled="loading"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<BasicButton class="btn-primary w-full" type="submit" :loading="loading">Vérifier</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { onMounted, ref } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { onBeforeRouteLeave } from "vue-router";

	defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, loading, errors, owner_info, vehicle_info } = storeToRefs(demandStore);

	const askModalIsOpen = ref(false);
	const certificate = ref(null);

	onMounted(() => {
		askModalIsOpen.value = true;
	});

	const load = async () => {
		await demandStore.getCertificatInfo(demand.value.sale_declaration_reference).then((res) => {
			certificate.value = res;
			owner_info.value = res.buyer;
			vehicle_info.value = res.sold_vehicle;
			demand.value.vin = res.sold_vehicle.vin;
			askModalIsOpen.value = false;
		});
	};

	onBeforeRouteLeave(() => {
		askModalIsOpen.value = false;
	});
</script>

<style scoped></style>
