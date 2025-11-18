import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";
import { formatURLSearchParams } from "@/helpers/utils";

export const usePrintOrderStore = defineStore("print-order", {
	state: () => ({
		orders: [],
		meta: undefined,
		order: null,
		errors: {},
		formData: null,
		loading: true,
		formLoading: false,
		reason: null,
	}),
	getters: {},
	actions: {
		fetchOrders(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "print-orders?" + formatURLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						if (response.data) {
							this.orders = response.data;
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
							this.orders = response;
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
		getOrder(id) {
			return this.orders.find((order) => order.id === id) || this.fetchOrder(id);
		},
		fetchOrder(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `print-orders/${id}`,
				})
					.then((response) => {
						this.order = response.data;
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
		searchOrder(reference) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `print-orders/search?q=${reference}`,
				})
					.then((response) => {
						this.order = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
						this.loading = false;
					});
			});
		},
		createOrder(data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "print-orders",
					data,
					headers: {
						...client.defaults.headers,
						"Content-Type": "multipart/form-data",
					},
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors || [];
						reject(error.response.data.message);
					})
					.finally(() => {
						this.formLoading = false;
					});
			});
		},
		updateOrder(id, data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `print-orders/${id}`,
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
		deleteOrder(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `print-orders/${id}`,
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
		loadForm() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `print-orders/create`,
				})
					.then((response) => {
						this.formData = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data.message);
					});
			});
		},
		reset() {
			this.orders = [];
			this.meta = {};
			this.order = null;
			this.loading = false;
			this.formLoading = false;
			this.errors = {};
			this.formData = null;
		},
		clear() {
			this.order = null;
		},
	},
});
