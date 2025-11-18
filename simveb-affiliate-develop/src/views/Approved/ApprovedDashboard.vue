<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<div v-if="can('show-wallet')" class="flex px-4 font-bold text-base justify-between items-center">
				<span>Portefeuille : {{ formatPrice(balance) }}</span>
			</div>
			<BasicButton v-if="can('update-wallet')" class="btn btn-primary shadow-md mr-2" @click="modal = true">
				Rechargez votre portefeuille
			</BasicButton>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12">
			<div class="grid grid-cols-12 gap-6">
				<DashboardStats />
			</div>
		</div>
	</div>

	<Modal v-if="can('update-wallet')" :show="modal" is-form @hidden="modal = false" @submit="rechargeWallet">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<span class="text-xl font-bold text-center mb-4">Rechargez votre portefeuille</span>

				<div class="mt-4">
					<TextInputGroup v-model="recharge.amount" label="Montant de la recharge" name="montant" required />
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton :loading="recharging" class="btn-primary w-full" type="submit"> Recharger</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import client from "@/assets/js/axios/client.js";
	import { onMounted, ref } from "vue";
	import { formatPrice } from "@/helpers/utils.js";
	import BasicButton from "@/components/BasicButton.vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import Swal from "sweetalert2";
	import { launchFedapayCheckout } from "@/assets/js/fedapayCheckout.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const balance = ref(0);
	const recharging = ref(false);
	const modal = ref(false);
	const { can } = userHasPermissions();

	const authStore = useAuthStore();

	const { user } = storeToRefs(authStore);

	const recharge = {
		amount: 0,
		telephone: "",
	};

	const rechargeWallet = () => {
		modal.value = false;

		launchFedapayCheckout(null, `Rechargement de compte`, recharge.amount, {
			firstname: user.value.firstname,
			lastname: user.value.lastname,
			phone: user.value.telephone,
			email: user.value.email,
		}).then((transaction) => {
			setRecharge(transaction, recharge.amount);
		});
	};

	const setRecharge = (transaction, amount) => {
		recharging.value = true;

		client({
			method: "POST",
			url: "/wallets/recharge",
			data: {
				amount: amount,
				payment_reference: transaction.id.toString(),
			},
		})
			.then((response) => response.data)
			.then(() => {
				Swal.fire({
					title: "Recharge effectuée",
					icon: "success",
				});

				loadWallet();
			})
			.finally(() => {
				recharging.value = false;

				modal.value = false;
			});
	};

	const loadWallet = () => {
		client({
			method: "GET",
			url: "/wallets/details",
		})
			.then((response) => response.data)
			.then((response) => {
				if (response !== "") balance.value = response.balance;
			});
	};

	onMounted(() => {
		can("show-wallet") && loadWallet();
	});
</script>

<style scoped></style>
