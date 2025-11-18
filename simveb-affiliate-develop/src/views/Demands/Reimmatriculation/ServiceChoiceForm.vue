<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Veuillez sélectionner le type d'immatriculation</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<template v-if="!demand.serviceCode">
			<ServiceCardSkeleton v-for="n in 5" :key="n" />
		</template>
		<template v-else>
			<div
				v-for="(service, index) in formData.services"
				:key="index"
				class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md"
			>
				<div class="relative group">
					<input
						:id="'service-select-' + service.code"
						v-model="demand.serviceCode"
						class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
						name="service-select"
						type="radio"
						:value="service.code"
					/>
					<label
						class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
						:for="'service-select-' + service.code"
					>
						<CallToActionCard
							image-alt="Cliquez pour choisir la demande d'immatriculation"
							:subtitle="service.description"
							:title="service.name"
						>
							<div
								class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-full p-2"
								:style="{ backgroundColor: service.color }"
							>
								<img
									v-if="service.image_url"
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									:src="service.image_url"
								/>
								<img
									v-else
									alt="Cliquez pour choisir la demande d'immatriculation"
									class=""
									src="@/assets/images/parc/apps.png"
								/>
								<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
									<i class="fa-light fa-check"></i>
								</span>
							</div>
						</CallToActionCard>
					</label>
				</div>
			</div>

			<div class="intro-y col-span-12">
				<div class="text-right mt-4">
					<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
						Annuler
					</button>
					<button
						class="btn btn-primary w-36"
						type="button"
						:disabled="!demand.serviceCode"
						@click="$emit('next')"
					>
						Suivant
					</button>
				</div>
			</div>
		</template>
	</div>

	<Modal :show="modalIsOpen" backdrop="static" @hidden="!demand.serviceCode ? $emit('next') : null">
		<ModalBody class="p-0">
			<div class="p-5 text-center font-bold">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width="24"
					height="24"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					stroke-width="2"
					stroke-linecap="round"
					stroke-linejoin="round"
					icon-name="alert-circle"
					data-lucide="alert-circle"
					class="lucide lucide-alert-circle w-20 h-20 text-info mx-auto mt-3"
				>
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="12" y1="8" x2="12" y2="12"></line>
					<line x1="12" y1="16" x2="12.01" y2="16"></line>
				</svg>
				<div class="text-2xl mt-5">Souhaitez vous conserver le même numéro d'immatriculation ?</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2 justify-between">
				<button
					class="btn btn-outline-primary w-1/2 me-2"
					type="button"
					@click="
						() => {
							demand.with_immatriculation = 1;
							closeModal();
						}
					"
				>
					Non, je veux changer
				</button>
				<button
					class="btn btn-primary w-1/2 ms-2"
					type="button"
					@click="
						() => {
							demand.with_immatriculation = 0;
							modalIsOpen = false;
						}
					"
				>
					Oui, conserver
				</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { onBeforeUnmount, onMounted, ref } from "vue";
	import CallToActionCard from "@/components/CallToActionCard.vue";
	import ServiceCardSkeleton from "@/components/Skeleton/ServiceCardSkeleton.vue";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";

	const demandStore = useDemandStore();
	const { demand, formData } = storeToRefs(demandStore);

	defineEmits(["prev", "next"]);

	const closeModal = () => {
		demand.value.serviceCode = "IMMATRICULATION_STANDARD";
		modalIsOpen.value = false;
	};

	const modalIsOpen = ref(false);

	onBeforeUnmount(() => {
		modalIsOpen.value = false;
	});

	onMounted(async () => {
		modalIsOpen.value = true;
	});
</script>

<style scoped></style>
