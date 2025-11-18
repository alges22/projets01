import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";

export const useVehicleStore = defineStore("gov-vehicles", {
	state: () => ({
		vehicles: [],
		meta: undefined,
		vehicle: {},
		errors: {},
		formData: null,
		loading: true,
		formLoading: false,
		formDataLoaded: false,
		url: "gov-vehicles",
	}),
	getters: {},
	actions: {
		fetchVehicles(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: this.url + new URLSearchParams(options).toString(),
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
		getVehicle(id) {
			return this.vehicles.find((vehicle) => vehicle.id === id) || this.fetchVehicle(id);
		},
		fetchVehicle(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `${this.url}/${id}`,
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
		createVehicle(data, importData = false, withFile = false) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: importData ? `import-${this.url}` : this.url,
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
		deleteVehicle(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `${this.url}/${id}`,
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
		validateVehicle(data) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: this.url,
					data,
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
		loadCreateData() {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `${this.url}/create`,
				})
					.then((response) => {
						this.formData = response.data;
						this.formDataLoaded = true;
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data?.message);
					});
				// .finally(() => {
				// 	this.loading = false;
				// });
			});
		},
		searchVehicle(vin) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/affiliate/vehicles/vehicle-details?vin=${vin}`,
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data.errors);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		reset() {
			this.vehicles = [];
			this.meta = undefined;
			this.vehicle = {};
			this.loading = true;
			this.formLoading = false;
			this.errors = {};
			this.formData = null;
		},
	},
});
