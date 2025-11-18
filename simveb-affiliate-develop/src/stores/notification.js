import { defineStore } from "pinia";
import client from "@/axios/client";

export const useNotificationStore = defineStore("notification", {
	state: () => ({
		notifications: [],
		notification: {},
		loading: true,
	}),
	getters: {},
	actions: {
		fetchNotifications(all = false) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/notifications" + (all ? "?_filter=all" : ""),
				})
					.then((response) => {
						this.notifications = response.data;
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
		markAsRead(id) {
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `/notifications/${id}/read`,
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
		markAllAsRead() {
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `/notifications/read/all`,
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
		delete() {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: "notifications/delete",
				})
					.then((response) => {
						this.notifications = [];
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors;
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
	},
});
