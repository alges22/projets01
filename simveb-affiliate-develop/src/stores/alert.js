import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";
import { formatURLSearchParams } from "@/helpers/utils";

export const useAlertStore = defineStore("alert", {
	state: () => ({
		alertTypes: [
			{
				name: "",
				code: "",
			},
		],
		alerts: [],
		alert: {},

		meta: undefined,
		loading: false,
		errors: [],
	}),
	getters: {},
	actions: {
		fetchAlertTypes() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/alert-types",
				})
					.then((response) => {
						this.alertTypes = response.data.map((type, index) => ({
							name: type.name,
							code: type.code,
							id: type.id,
						}));
						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},

		fetchAlerts(options) {
			this.loading = true;
			this.errors = {};

			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/vehicle-alerts?" + formatURLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						this.alerts = response.data;

						this.meta = response.meta;

						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},

		fetchAlertDetail({ id }) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/vehicle-alerts/${id}`,
				})
					.then((response) => response.data)
					.then((response) => {
						this.alert = response.data;

						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},

		makeAlert(data) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/vehicle-alerts",
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
	},
});
