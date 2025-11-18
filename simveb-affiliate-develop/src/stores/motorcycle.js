import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";

const API_URL = import.meta.env.VITE_API_URL;

export const useMotorcycleStore = defineStore("motorcycle", {
	state: () => ({
		vehicles: [],
		meta: undefined,
		vehicle: {},
		errors: {},
		url: "motorcycles",
		loading: true,
		formLoading: false,
		formDataLoaded: false,
		formData: {
			import_model: API_URL + "/motorcycle/file-format",
		},
	}),
	actions: {
		createVehicle(data, importData = false, withFile = false) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: importData ? "/motorcycle/import" : "motorcycles",
					data,
					headers: {
						...client.defaults.headers,
						"Content-Type": importData || withFile ? "multipart/form-data" : "application/json",
					},
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors || error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
					});
			});
		},
		updateVehicle(id, data, withFile = false) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `${this.url}/${id}`,
					data: {
						...data,
						_method: "PUT",
					},
					headers: {
						...client.defaults.headers,
						"Content-Type": withFile ? "multipart/form-data" : "application/json",
					},
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors || error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
					});
			});
		},
		fetchVehicles(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "motorcycles" + new URLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						if (response.data) {
							this.vehicles = response.data;
							this.meta = {
								current_page: response.current_page,
								total: response.total,
								per_page: response.per_page,
								from: response.from,
								to: response.to,
								links: response.links,
							};
							resolve(response.data);
						} else {
							this.vehicles = response;
							resolve(response);
						}
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
		fetchVehicle(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `motorcycles/${id}`,
				})
					.then((response) => {
						this.vehicle = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors || error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		deleteVehicle(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `motorcycles/${id}`,
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
					});
			});
		},
		getFilePath() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/motorcycle/file-format",
				}).then((response) => resolve(response.data));
			});
		},
	},
});
