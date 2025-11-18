<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Vérifier les informations du véhicule</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12" @submit.prevent="submit">
			<div class="intro-y box p-5 flex justify-between items-end">
				<div class="w-full">
					<TextInputGroup
						v-model="demand.vin"
						name="vin"
						label="Entrer le VIN du véhicule"
						placeholder="VN1324718265422NVV"
						add-class="w-full"
						:disabled="loading"
						:errors="errors.vin"
						required
						auto-complete="vin"
					/>
				</div>
				<div class="ms-2 w-1/5">
					<BasicButton class="btn-primary h-auto w-full" type="submit" :loading="loading">
						Suivant
					</BasicButton>
				</div>
			</div>
		</form>

		<div v-if="reason" class="rounded-xl bg-white p-4 col-span-12">
			<div class="p-1 border-l-4 border-l-primary">
				<span class="ms-4 font-bold text-lg text-primary">{{ reason.title }}</span>
			</div>
		</div>

		<form v-if="reason" class="intro-y col-span-12" @submit.prevent="$emit('next')">
			<div class="intro-y box p-5 flex-col justify-between items-end">
				<div v-if="reason.code === 'M-AC'" class="w-full mb-4">
					<TextAreaInputGroup
						v-model="demand.custom_reason"
						name="custom_reason"
						label="Entrer la raison de ré-immatriculation"
						:disabled="loading"
						:errors="errors.custom_reason || []"
						required
					/>
				</div>
				<div class="w-full mb-4">
					<TextInputGroup
						v-model="demand.customs_ref"
						name="customs_ref"
						label="Entrer le numéro de la quittance de douane"
						add-class="w-full"
						:disabled="loading"
						:errors="errors.customs_ref || []"
						required
					/>
				</div>
				<div class="w-1/5 ml-auto">
					<BasicButton class="w-full h-auto btn-primary" type="submit" :loading="loading">
						Suivant
					</BasicButton>
				</div>
			</div>
		</form>
	</div>

	<Modal :show="modalIsOpen" size="modal-lg" @hidden="modalIsOpen = false">
		<ModalHeader class="pt-6">
			<button class="absolute right-0 top-0 mt-6 mr-3" @click="modalIsOpen = false">
				<i class="fa-solid fa-x w-8 h-4 font-bold" />
			</button>
		</ModalHeader>
		<ModalBody>
			<template v-if="reason">
				<div class="block font-bold text-center text-xl mb-4">{{ reason.title }}</div>
				<div class="flex-none relative block before:block before:w-full before:pt-[50%]">
					<div class="absolute top-0 left-0 w-full h-full image-fit">
						<img :alt="reason.title" :src="reason.img_url" class="!object-fill" />
					</div>
				</div>
			</template>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-primary w-full" type="button" @click="modalIsOpen = false">Suivant</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import { ref } from "vue";
	import TextAreaInputGroup from "@/components/Form/TextAreaInputGroup.vue";

	const demandStore = useDemandStore();
	const { demand, loading, errors, reason } = storeToRefs(demandStore);
	const modalIsOpen = ref(false);

	defineEmits(["next", "prev"]);

	const submit = () => {
		demandStore.fetchVehicleSituation(demand.value.vin).then(() => {
			modalIsOpen.value = true;
			demand.value.reason_id = reason.value.id;
		});
	};
</script>
