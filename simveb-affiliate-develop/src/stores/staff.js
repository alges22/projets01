import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";

export const useStaffStore = defineStore("staff", {
	state: () => ({
		staffs: [],
		meta: undefined,
		staff: {},
		errors: {},
		formData: null,
		loading: true,
		formLoading: false,
		formDataLoaded: false,
	}),
	getters: {},
	actions: {
		fetchStaffs(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/profile-types/members?" + new URLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						if (response.data) {
							this.staffs = response.data;
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
							this.staffs = response;
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
		getStaff(id) {
			return this.staffs.find((staff) => staff.id === id) || this.fetchStaff(id);
		},
		fetchStaff(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/profile-types/members/${id}`,
				})
					.then((response) => {
						this.staff = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors || error.response.data;
						reject(error.response.data.message);
					});
				/*.finally(() => {
						this.loading = false;
					});*/
			});
		},
		createStaff(data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/profile-types/members",
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
		sendInvitation(data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/invitations",
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
		updateStaff(id, data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `/profile-types/members/${id}`,
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
		deleteStaff(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `/profile-types/members/${id}`,
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
					url: `/invitations/create`,
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
		suspendStaff(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "PUT",
					url: `/profile-types/members/toggle-status`,
					data: {
						profile_id: id,
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
		reset() {
			this.s = [];
			this.meta = undefined;
			this.staff = {};
			this.loading = true;
			this.formLoading = false;
			this.errors = {};
			this.formData = null;
		},
	},
});
