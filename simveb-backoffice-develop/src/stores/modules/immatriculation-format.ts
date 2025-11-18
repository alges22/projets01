import { defineStore } from "pinia";
import client from "/@src/composable/axiosClient";

export const useImmatriculationFormatStore = defineStore("immatriculation-format", {
	state: () => ({
		formats: [],
		meta: undefined,
		format: {},
		errors: {},
		loading: false,
		formLoading: false,
		components: [],
		categories: [],
		profiles: [],
	}),
	getters: {},
	actions: {
		fetchFormats(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "immatriculation-formats?" + new URLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						if (response.data) {
							this.formats = response.data;
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
							this.formats = response;
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
		getFormat(id: string) {
			return this.formats.find((format) => format.id === id) || this.fetchFormat(id);
		},
		fetchFormat(id: string) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `immatriculation-formats/${id}`,
				})
					.then((response) => {
						resolve(response.data);
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
		editFormat(id: string) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `immatriculation-formats/${id}/edit`,
				})
					.then((response) => {
						this.format = response.data.immatriculation_format;
						this.components = response.data.immatriculation_format.components;
						this.categories = response.data.vehicle_categories;
						this.profiles = response.data.profile_types;
						resolve(response.data);
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
		createFormat(data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "immatriculation-formats",
					data,
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
		updateFormat(id, data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `immatriculation-formats/${id}`,
					data: {
						...data,
						_method: "PUT",
					},
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
		deleteFormat(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `immatriculation-formats/${id}`,
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
		loadCreateData() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `immatriculation-formats/create`,
				})
					.then((response) => response.data)
					.then((response) => {
						this.categories = response.vehicle_categories;
						this.components = response.components;
						this.profiles = response.profile_types;
						resolve(response);
					})
					.catch((error) => {
						reject(error.response.data.message);
					});
			});
		},
		makeRequest(method = "POST", data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: method,
					url: `immatriculation-formats/${id}`,
					data,
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
		reset() {
			this.formats = [];
			this.meta = {};
			this.format = {};
			this.loading = false;
			this.formLoading = false;
			this.errors = {};
		},
	},
});
