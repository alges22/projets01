import { defineStore } from "pinia";
import client from "/@src/composable/axiosClient";

export const useAffiliateStore = defineStore("affiliate", {
	state: () => ({
		row: {},
		errors: {},
		validateLoading: false,
		rejectLoading: false,
		invitationLoading: false,
	}),
	getters: {},
	actions: {
		validateRequest(id: string) {
			this.validateLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/affiliate-registration-requests/validate/" + id,
				})
					.then((response) => {
						this.row = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.validateLoading = false;
					});
			});
		},
		rejectRequest(id: string, data) {
			this.rejectLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/affiliate-registration-requests/reject/" + id,
					data: data,
				})
					.then((response) => {
						this.row = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.rejectLoading = false;
					});
			});
		},
		loadInvitationCreateData() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/affiliate/invitation/create",
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data.message);
					});
			});
		},
		inviteMember(data) {
			this.invitationLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/affiliate/invite",
					data: data,
				})
					.then((response) => {
						this.row = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.invitationLoading = false;
					});
			});
		},
		reset() {
			this.row = {};
			this.validateLoading = false;
			this.rejectLoading = false;
			this.invitationLoading = false;
			this.errors = {};
		},
	},
});
