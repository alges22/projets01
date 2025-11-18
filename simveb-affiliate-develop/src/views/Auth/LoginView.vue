<template>
	<div>
		<div class="container sm:px-10">
			<div class="block">
				<div class="h-screen flex py-5 xl:py-0 my-10 xl:my-0">
					<form
						class="my-auto mx-auto bg-white dark:bg-darkmode-600 px-5 sm:px-8 py-8 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4"
						@submit.prevent="sendOTP(false)"
					>
						<router-link to="/" class="-intro-x flex items-center pt-5 mb-8 justify-center">
							<BrandLogo class="w-1/2" />
						</router-link>
						<h2 class="intro-x font-bold text-2xl xl:text-3xl text-center">Se connecter</h2>
						<div class="intro-x mt-2 text-slate-400 text-center">
							Système intégré d'immatriculation et d'identification électronique des véhicules en
							circulation au Bénin
						</div>
						<div class="intro-x mt-8">
							<text-input-group
								v-model="npi"
								name="npi"
								placeholder="NPI"
								required
								add-class="intro-x login__input py-3 px-4 block"
								type="text"
								pattern="[0-9]{10}"
								:maxlength="10"
								:minlength="10"
							/>
						</div>
						<div class="intro-x mt-5 xl:mt-8 text-center">
							<BasicButton
								type="submit"
								class="btn-primary py-3 px-4 w-full xl:w-48 xl:mr-3 align-top"
								:loading="loading"
								:disabled="loading"
							>
								Valider
							</BasicButton>
						</div>
					</form>
				</div>
			</div>
		</div>

		<Modal :show="otpModal" @hidden="otpModal = false" @shown="focus()">
			<ModalHeader class="pt-6">
				<h2 class="font-bold text-base mr-auto">Vérification du code OTP</h2>
			</ModalHeader>
			<ModalBody>
				<div class="flex flex-col space-y-5">
					<p class="font-bold">
						Entrez le code de vérification envoyé par SMS au numéro de téléphone {{ telephone }}.
					</p>
					<InputCode v-model="otpCode" :length="4" class="mx-6" @submit="submit" />

					<div class="flex flex-col space-y-5">
						<div class="flex flex-row items-center justify-center text-center text-sm font-bold space-x-1">
							<p>Vous n’avez pas reçu le code ?</p>
							<button
								class="flex flex-row items-center text-blue-600 hover:underline focus:outline-none"
								@click="sendOTP(true)"
							>
								Renvoyer le code
							</button>
						</div>
						<div>
							<BasicButton
								class="btn-primary w-full mx-2"
								type="button"
								:loading="loading"
								:disabled="loading || otpCode.length < 4"
								@click="submit"
							>
								Valider
							</BasicButton>
						</div>
					</div>
				</div>
			</ModalBody>
		</Modal>
	</div>
</template>

<script setup>
	import dom from "@left4code/tw-starter/dist/js/dom";
	import { onMounted, ref } from "vue";
	import BrandLogo from "@/components/logo/BrandLogo.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import Alert from "@/components/notification/alert.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { Modal, ModalBody, ModalHeader } from "@/global-components/modal/index.js";
	import InputCode from "@/components/Form/InputCode.vue";
	import { storeToRefs } from "pinia";
	import BasicButton from "@/components/BasicButton.vue";

	const authStore = useAuthStore();
	const { loading } = storeToRefs(authStore);
	const npi = ref("");
	const otpCode = ref("");
	const otpModal = ref(false);
	const telephone = ref("");
	const duration = ref(0);

	const sendOTP = (resend = false) => {
		authStore.sendOTP(npi.value, resend).then((data) => {
			otpModal.value = true;
			telephone.value = data.telephone;
			duration.value = data.otp_duration;
			Alert.success(data.message);
		});
	};

	const focus = () => {
		document.getElementById("otpCodeInput0").focus();
	};

	const submit = () => {
		authStore
			.login({ username: npi.value, password: otpCode.value })
			.then(() => {
				window.location.href = "/";
			})
			.catch(() => {
				otpCode.value = "";
			});
	};

	onMounted(() => {
		dom("body").removeClass("main").removeClass("error-page").addClass("login");
	});
</script>
