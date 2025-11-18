import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";

export const useBlacklistedVehicleStore = defineStore("blacklistedVehicle", {
	state: () => ({
		blacklist: [],
		vehicle: {},
	}),
	getters: {
		getRegistrationNumber: (state) => state.registrationNumber,
	},
	actions: {
		fetchBlacklistedVehicles() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/blacklist-vehicles",
				})
					.then((response) => response.data)
					.then((response) => {
						this.blacklist = response.data;

						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},

		addToBlackList(data) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/blacklist-vehicles",
					data: data,
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						if (error.response.data.errors) {
							this.errors = error.response.data.errors;
						}
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},

		fetchBlacklistedVehicleDetail({ id }) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/blacklist-vehicles/${id}`,
				})
					.then((response) => response.data)
					.then((response) => {
						this.vehicle = response.data;

						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},
	},
});
