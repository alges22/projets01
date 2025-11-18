<template>
	<Modal
		:show="modalIsOpen"
		is-form
		backdrop="static"
		@hidden="closeModal"
		@submit.prevent="submitCode"
		@shown="focus()"
	>
		<ModalHeader class="pt-6">
			<h2 class="font-bold text-base mr-auto">Vérification du code OTP</h2>
			<button type="button" class="absolute right-0 top-0 mt-6 mr-3" @click="closeModal">
				<i class="fa-solid fa-x w-8 h-4 font-bold" />
			</button>
		</ModalHeader>
		<ModalBody>
			<LoaderSpinner v-if="loading" type="block" />
			<div v-else class="flex flex-col space-y-5">
				<p class="font-bold">{{ message }}</p>
				<InputCode v-model="inputCode" :length="4" class="mx-6" @submit="submitCode" />

				<div class="flex flex-col space-y-5">
					<div class="flex flex-row items-center justify-center text-center text-sm font-bold space-x-1">
						<p>Vous n’avez pas reçu le code ?</p>
						<button
							class="flex flex-row items-center text-blue-600"
							:disabled="loading"
							type="button"
							@click="sendOTP"
						>
							Renvoyer le code
						</button>
					</div>
					<div class="flex flex-row items-center justify-center text-center text-sm font-bold space-x-1">
						<p>Ce code expire dans {{ timeout }} secondes.</p>
					</div>
					<div>
						<BasicButton :loading="formLoading" class="btn-primary w-full mx-2" type="submit">
							Valider
						</BasicButton>
					</div>
				</div>
			</div>
		</ModalBody>
	</Modal>
</template>

<script setup>
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import InputCode from "@/components/Form/InputCode.vue";
	import { ref, watch } from "vue";
	import BasicButton from "@/components/BasicButton.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { useIntervalFn } from "@vueuse/core";
	import { useOtpStore } from "@/stores/otp.js";
	import { storeToRefs } from "pinia";
	import Alert from "@/components/notification/alert.js";
	import { debounce } from "@/helpers/utils.js";

	const props = defineProps({
		open: {
			type: Boolean,
			required: true,
		},
		npi: {
			type: String,
			required: true,
		},
		isCompany: {
			type: Boolean,
			required: true,
		},
	});

	const emit = defineEmits(["submit", "close", "resend", "loading", "loaded"]);

	const otpStore = useOtpStore();
	const { loading, formLoading, timeout, authorization_id } = storeToRefs(otpStore);
	const modalIsOpen = ref(false);
	const inputCode = ref(null);
	const message = ref("");
	const { pause, resume } = useIntervalFn(
		() => {
			if (timeout.value <= 0) pause();
			else timeout.value = timeout.value - 1;
		},
		1000,
		{ immediate: false }
	);

	const sendOTP = debounce(async () => {
		emit("loading");
		await otpStore
			.sendOtp(props.npi, props.isCompany)
			.then((res) => {
				Alert.success(res.message);
				message.value = res.message;
				modalIsOpen.value = true;
				resume();
			})
			.catch(() => {
				emit("close");
			})
			.finally(() => {
				emit("loaded");
			});
	}, 500);

	const focus = () => {
		document.getElementById("otpCodeInput0").focus();
	};

	const submitCode = async () => {
		await otpStore
			.validateOtp(inputCode.value)
			.then((res) => {
				modalIsOpen.value = false;
				Alert.success(res.message);
			})
			.then(() => {
				emit("submit", authorization_id.value);
			});
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		emit("close");
	};

	watch(
		() => props.open,
		(value) => {
			if (value) {
				sendOTP();
			} else {
				modalIsOpen.value = false;
			}
		}
	);
</script>

<style scoped></style>
