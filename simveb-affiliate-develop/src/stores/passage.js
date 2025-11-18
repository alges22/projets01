import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";
import { formatURLSearchParams } from "@/helpers/utils";

export const useVehiclePassageStore = defineStore("vehiclePassage", {
	state: () => ({
		passage: {},
		passages: [],
		immatriculationNumber: null,
		vehicleInfo: null,
		registrationId: null,
		meta: undefined,
		loading: false,
		errors: [],
		formLoading: false,
	}),
	getters: {},
	actions: {
		fetchVehicle(immatriculation_number) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/vehicle-passages/get-vehicle-infos",
					data: {
						immatriculation_number,
					},
				})
					.then((response) => {
						this.vehicleInfo = response.data;
						this.passage.vehicle_id = response.data.vehicle.id;
						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
						this.errors = error.response.data.errors;
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		registerPassage(data) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "/vehicle-passages",
					data: data,
					headers: {
						...client.defaults.headers,
						"Content-Type": "multipart/form-data",
					},
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
						this.formLoading = false;
					});
			});
		},

		fetchPassages(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/vehicle-passages?" + formatURLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						this.passages = response.data;
						this.meta = {
							current_page: response.current_page,
							total: response.total,
							per_page: response.per_page,
							from: response.from,
							to: response.to,
							links: response.links,
						};
						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},
		fetchPassageDetail(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/vehicle-passages/${id}`,
				})
					.then((response) => {
						this.passage = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},

		fetchCountryOptions() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/vehicle-passages/create",
				})
					.then((response) => {
						this.countryOptions = response.data.countries;

						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},

		fetchVehiclePassageHistory({ id }) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/vehicle-passages`,
					params: {
						immatriculation_number: id,
					},
				})
					.then((response) => response.data)
					.then((response) => {
						this.history = response.data;

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
	},
});
