import { defineStore } from "pinia";
import client from "@/assets/js/axios/client.js";

export const useOtpStore = defineStore("otp", {
	state: () => ({
		expired_at: null,
		timeout: null,
		loading: true,
		formLoading: false,
		authorization_id: null,
	}),
	getters: {
		getTimeLeft: (state) => {
			return state.expired_at - Date.now();
		},
	},
	actions: {
		sendOtp(npi, isCompany = false) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/client/send-demand-otp",
					data: isCompany ? { owner_ifu: npi } : { owner_npi: npi },
				})
					.then((response) => {
						this.authorization_id = response.data.authorization_id;
						this.timeout = Number.parseInt(response.data.expire_in);
						this.expired_at = response.data.expire_at;
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		validateOtp(code) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: "/client/verify-demand-otp",
					data: {
						owner_otp: code,
						authorization_id: this.authorization_id,
					},
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
					});
			});
		},
	},
});
