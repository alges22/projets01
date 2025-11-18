<template>
	<div class="auth-wrapper-inner is-single">
		<!--Fake navigation-->
		<div class="auth-nav">
			<div class="left"></div>
			<div class="center pt-6">
				<RouterLink to="/" class="header-item">
					<img alt="logo" src="/images/logos/logo/logo_anatt.png" />
				</RouterLink>
			</div>
			<div class="right">
				<label
					class="dark-mode ml-auto"
					tabindex="0"
					role="button"
					@keydown.space.prevent="(e) => (e.target as HTMLLabelElement).click()"
				>
					<input
						data-cy="dark-mode-toggle"
						type="checkbox"
						:checked="!darkmode.isDark"
						@change="darkmode.onChange"
					/>
					<span></span>
				</label>
			</div>
		</div>

		<!--Single Centered Form-->
		<div class="single-form-wrap">
			<div class="inner-wrap">
				<!--Form Title-->
				<div class="auth-head">
					<h2>Bienvenue sur SIMVeB.</h2>
					<p>Veuillez vous connecter</p>
				</div>

				<!--Form-->
				<div class="form-card">
					<form @submit.prevent="sendOTP(false)">
						<div class="login-form">
							<VField>
								<VControl icon="key">
									<VInput
										v-model="npi"
										type="text"
										placeholder="NPI"
										autocomplete="npi"
										name="npi"
										required
										pattern="[0-9]{10}"
										:maxlength="10"
										:minlength="10"
									/>
								</VControl>
							</VField>

							<div class="login">
								<VButton :loading="loading" type="submit" color="primary" bold fullwidth raised>
									Connexion
								</VButton>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<VModal
			is="form"
			:open="otpModal"
			actions="right"
			title="Vérification du code OTP"
			:noclose="true"
			@submit.prevent="handleLogin"
			@close="otpModal = false"
		>
			<template #content>
				<div class="modal-form">
					<h3 class="has-text-centered">
						Entrez le code de vérification envoyé par SMS au numéro de téléphone {{ telephone }}.
					</h3>
					<InputCode v-model="otpCode" :length="4" class="mt-6 mb-6" @submit="handleLogin" />

					<div class="forgot-link has-text-left">
						Vous n’avez pas reçu le code ?
						<a href="#" class="" @click="sendOTP(true)"> Renvoyer le code </a>
					</div>
					<div class="forgot-link has-text-left">Le code expirera dans {{ duration }} minutes</div>
				</div>
			</template>
			<template #action>
				<VButton
					:loading="loading"
					:disabled="loading || otpCode.length < 4"
					color="primary"
					raised
					type="submit"
				>
					Valider
				</VButton>
			</template>
		</VModal>
	</div>
</template>

<script setup lang="ts">
	import { useDarkmode } from "/@src/stores/darkmode";
	import { useUserSession } from "/@src/stores/userSession";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { storeToRefs } from "pinia";
	import InputCode from "/@src/components/base/form/VInputCode.vue";

	const darkmode = useDarkmode();
	const notyf = useNotyf();
	const userSession = useUserSession();
	const { loading } = storeToRefs(userSession);
	const npi = ref("");
	const otpCode = ref("");
	const otpModal = ref(false);
	const telephone = ref("");
	const duration = ref(0);

	const sendOTP = (resend = false) => {
		userSession.sendOTP(npi.value, resend).then((data) => {
			otpModal.value = true;
			telephone.value = data.telephone;
			duration.value = data.otp_duration;
			notyf.success(data.message);
		});
	};

	const handleLogin = async () => {
		userSession
			.login({
				username: npi.value,
				password: otpCode.value,
			})
			.then(async () => {
				notyf.dismissAll();
				notyf.success("Content de vous revoir!");
				window.location.href = "/";
			});
	};
</script>
