<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold m-auto">Impression de la plaque</h2>
	</div>

	<form novalidate class="mt-5" @submit.prevent="submit">
		<div class="flex items-center bg-gray-100">
			<div class="relative w-full flip-container h-[36rem] overflow-hidden" :class="{ flip: isFlipped }">
				<!--				Formulaire avant-->
				<div
					class="absolute w-full bg-blue-100 shadow-lg border-2 border-blue-500 rounded-md backface-hidden transform transition-transform duration-1000 front"
					:class="{ '-rotate-y-180': isFlipped }"
				>
					<div class="p-3 flex items-center border-b justify-center">
						<p class="text-xl leading-5 text-blue-700">Plaque avant</p>
					</div>

					<div class="p-5">
						<div class="mb-2">
							<TextInputGroup
								v-model="form.front_plate"
								name="front_plate"
								label="Entrer le numéro de série"
								placeholder="##########"
								add-class="w-full"
								:disabled="loading"
								:errors="errors.front_plate"
								required
								auto-complete="front_plate"
							/>
						</div>
						<div class="mb-2">
							<TextInputGroup
								v-model="form.front_plate_rfid"
								name="front_plate_rfid"
								label="Entrer le RFID"
								placeholder="###########"
								add-class="w-full"
								:disabled="loading"
								:errors="errors.front_plate_rfid"
								required
								auto-complete="front_plate_rfid"
							/>
						</div>
						<div class="col-span-full">
							<FileDropZone
								:disabled="loading"
								label="Image de la plaque"
								:errors="errors.images"
								name="front-image"
								:multiple="false"
								@update:model-value="(file) => addFile(file, true)"
							/>
						</div>
						<div class="flex items-center justify-center">
							<button type="button" class="mt-4 p-2 bg-blue-500 text-white rounded" @click="flipForm">
								<i
									class="fa fa-money-check-dollar fa-flip fa-xl me-2"
									style="--fa-animation-duration: 2s"
								></i>
								Passer à la plaque arrière
							</button>
						</div>
					</div>
				</div>

				<!--				Formulaire arrière-->

				<div
					class="absolute w-full bg-white shadow-lg border-2 border-primary-dark rounded-md backface-hidden transform transition-transform duration-1000 back rotate-y-180"
					:class="{ '-rotate-y-180': !isFlipped }"
				>
					<div class="p-3 flex items-center border-b justify-center">
						<p class="text-xl leading-5">Plaque arrière</p>
					</div>

					<div class="p-5">
						<div class="mb-2">
							<TextInputGroup
								v-model="form.back_plate"
								name="back_plate"
								label="Entrer le numéro de série"
								placeholder="##########"
								add-class="w-full"
								:disabled="loading"
								:errors="errors.back_plate"
								required
								auto-complete="back_plate"
							/>
						</div>
						<div class="mb-2">
							<TextInputGroup
								v-model="form.back_plate_rfid"
								name="back_plate_rfid"
								label="Entrer le RFID"
								placeholder="###########"
								add-class="w-full"
								:disabled="loading"
								:errors="errors.back_plate_rfid"
								required
								auto-complete="back_plate_rfid"
							/>
						</div>
						<div class="col-span-full">
							<FileDropZone
								:disabled="loading"
								label="Image de la plaque"
								:errors="errors.images"
								name="back-image"
								:multiple="false"
								@update:model-value="(file) => addFile(file, false)"
							/>
						</div>
						<div class="flex items-center justify-center">
							<button type="button" class="mt-4 p-2 bg-primary-dark text-white rounded" @click="flipForm">
								<i
									:class="{
										'!hidden': !isFlipped,
									}"
									class="fa fa-money-check-dollar fa-flip fa-xl me-2"
									style="--fa-animation-duration: 2s"
								></i>
								Passer à la plaque avant
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="flex align-center justify-end mt-5">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.back()">
				Retour
			</button>
			<BasicButton :loading="loading" class="btn btn-primary w-36" type="submit">Enregistrer</BasicButton>
		</div>
	</form>

	<SuccessModalComponent
		:open="successModalOpen"
		message="L'impression des plaques a été confirmé"
		@close="
			() => {
				successModalOpen = false;
				$router.back();
			}
		"
	/>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { usePrintOrderStore } from "@/stores/print-order.js";
	import BasicButton from "@/components/BasicButton.vue";
	import FileDropZone from "@/components/Form/FileDropZone.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import { ref } from "vue";
	import client from "@/assets/js/axios/client.js";
	import Alert from "@/components/notification/alert.js";
	import SuccessModalComponent from "@/components/SuccessModalComponent.vue";

	const orderStore = usePrintOrderStore();
	const { formLoading: loading, errors } = storeToRefs(orderStore);
	const form = ref({
		images: {
			front: null,
			back: null,
		},
	});
	const successModalOpen = ref(false);
	const isFlipped = ref(false);

	const flipForm = () => {
		isFlipped.value = !isFlipped.value;
	};

	const props = defineProps({
		id: String,
	});

	const submit = async () => {
		if (!form.value.front_plate || !form.value.front_plate_rfid || !form.value.images.front) {
			Alert.warn("Veuillez remplir tous les champs de la plaque avant", true);
			isFlipped.value ? flipForm() : null;
			return;
		}
		if (!form.value.back_plate || !form.value.back_plate_rfid || !form.value.images.back) {
			Alert.warn("Veuillez remplir tous les champs de la plaque arrière", true);
			!isFlipped.value ? flipForm() : null;
			return;
		}

		errors.value = {};
		loading.value = true;
		await client({
			method: "POST",
			url: "print-orders/print-plate",
			data: {
				...form.value,
				print_order_id: props.id,
				images: [form.value.images.front, form.value.images.back],
			},
			headers: {
				...client.defaults.headers,
				"Content-Type": "multipart/form-data",
			},
		})
			.then((response) => {
				Alert.success(response.data.message);
				successModalOpen.value = true;
			})
			.catch((error) => {
				errors.value = error.response.data.errors || [];
			})
			.finally(() => {
				loading.value = false;
			});
	};

	const addFile = (file, isFront = true) => {
		isFront ? (form.value.images.front = file) : (form.value.images.back = file);
	};
</script>

<style scoped>
	.flip-container {
		perspective: 1000px;
	}

	.front,
	.back {
		backface-visibility: hidden;
	}

	.rotate-y-180 {
		transform: rotateY(180deg);
	}
	.-rotate-y-180 {
		transform: rotateY(-180deg);
	}

	.flip .front {
		transform: rotateY(-180deg);
	}

	.flip .back {
		transform: rotateY(0deg);
	}
</style>
