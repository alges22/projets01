import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";

export const useInvitationStore = defineStore("invitation", {
	state: () => ({
		invitations: [],
		invitation: {},
		loading: true,
	}),
	getters: {},
	actions: {
		fetchInvitations(all = false) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/invitations" + (all ? "?_filter=all" : ""),
				})
					.then((response) => {
						this.invitations = response.data;
						resolve(response);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		validate(id, validate = true) {
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `/invitations/${id}/${validate ? "validate" : "deny"}`,
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors || [];
						reject(error.response.data.message);
					});
			});
		},
	},
});
