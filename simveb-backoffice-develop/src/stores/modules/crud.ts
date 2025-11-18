import { defineStore } from "pinia";
import client from "/@src/composable/axiosClient";

const formDataHeaders = {
	...client.defaults.headers,
	"Content-Type": "multipart/form-data",
};

export const useCrudStore = defineStore("crud", {
	state: () => ({
		url: "",
		rows: [],
		meta: undefined,
		row: {},
		errors: {},
		formData: null,
		loading: true,
		formLoading: false,
		formDataLoaded: false,
	}),
	getters: {},
	actions: {
		fetchRows(options: Object) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: this.url + "?" + new URLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						if (response.data) {
							this.rows = response.data;
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
							this.rows = response;
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
		getRow(id: string) {
			return this.rows.find((row) => row.id === id) || this.fetchRow(id);
		},
		fetchRow(id: string) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `${this.url}/${id}`,
				})
					.then((response) => {
						this.row = response.data;
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
		createRow(data: Object) {
			this.errors = {};
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
						this.errors = error.response.data.errors || error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
					});
			});
		},
		createWithFile(data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: this.url,
					headers: formDataHeaders,
					data: { ...data },
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
		updateRow(id, data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `${this.url}/${id}`,
					data: {
						...data,
						_method: "PUT",
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
		updateWithFile(id: string, data: Object) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `${this.url}/${id}`,
					headers: formDataHeaders,
					data: {
						...data,
						_method: "PUT",
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
		deleteRow(id) {
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
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		loadEditData(id: string) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `${this.url}/${id}/edit`,
				})
					.then((response) => {
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
		makeRequest(method = "POST", data: Object | String, url: string | null = null) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: method,
					url: url ?? `${this.url}`,
					data,
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
		reset() {
			this.rows = [];
			this.meta = undefined;
			this.row = {};
			this.loading = true;
			this.formLoading = false;
			this.errors = {};
			this.formData = null;
		},
	},
});
