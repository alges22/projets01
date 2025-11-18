import { defineStore } from "pinia";
import client from "@/assets/js/axios/client.js";

export const useCartStore = defineStore("cart", {
	state: () => ({
		cartModalOpen: false,
		items: [],
		itemsCount: null,
		loading: false,
		formLoading: false,
		cart: null,
		order: null,
		services: [],
	}),
	getters: {},
	actions: {
		openModal() {
			this.cartModalOpen = true;
		},
		closeModal() {
			this.cartModalOpen = false;
		},
		fetchItems() {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/client/cart",
				})
					.then((response) => response.data)
					.then((response) => {
						this.items = response.demands;
						this.itemsCount = response.demands.length;
						this.cart = {
							amount: response.amount,
						};
						this.services = response.extra_services || [];
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
		removeItem(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `/client/cart-remove-demand/${id}`,
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
		validateCart() {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/client/validate-cart",
				})
					.then((response) => {
						this.order = response.data.order;
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
		submitOrder(paymentReference, orderID) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/client/submit-order",
					data: {
						payment_reference: paymentReference,
						order_id: orderID,
					},
				})
					.then((response) => {
						this.itemsCount = null;
						this.order = response.data;
						resolve(response);
					})
					.catch((error) => {
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		fetchOrder(orderID) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/client/orders/${orderID}`,
				})
					.then((response) => response.data)
					.then((response) => {
						this.order = response;
						resolve(response);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					});
			});
		},
		generateInvoice(orderID) {
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `/client/invoices/${orderID}/generate`,
					responseType: "blob",
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data;
						reject(error.response.data.message);
					});
			});
		},
	},
});
