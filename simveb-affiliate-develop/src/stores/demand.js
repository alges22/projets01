import { defineStore } from "pinia";
import client from "@/assets/js/axios/client";
import { formatURLSearchParams } from "@/helpers/utils";

export const useDemandStore = defineStore("demand", {
	state: () => ({
		serviceId: null,
		demands: [],
		meta: undefined,
		demand: null,
		errors: {},
		formData: null,
		loading: true,
		loaded: false,
		formLoading: false,
		owner_info: null,
		buyer_info: null,
		vehicle_info: null,
		reason: null,
		update: false,
	}),
	getters: {},
	actions: {
		getServiceID(code) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `get-service-id/${code}`,
				})
					.then((response) => {
						this.serviceId = response.data;
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
		fetchDemands(options) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "client/demands?" + formatURLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						this.loaded = true;
						if (response.data) {
							this.demands = response.data;
							this.meta = response.meta;
							resolve(response.data);
						} else {
							this.demands = response;
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
		getDemand(id) {
			return this.demands.find((demand) => demand.id === id) || this.fetchDemand(id);
		},
		fetchDemand(id) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `client/demands/${id}`,
				})
					.then((response) => {
						this.demand = response.data;
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
		createDemand(data, addToCart = false) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: addToCart ? "client/add-demand-to-cart" : "client/demands",
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
		updateDemand(id, data) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `client/demands/${id}`,
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
		editDemand(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `client/demands/edit/${id}`,
				})
					.then((response) => {
						this.formData = response.data.service;
						this.demand = response.data.demand;
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
		deleteDemand(id) {
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "DELETE",
					url: `client/demands/${id}`,
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
		loadForm(serviceId) {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `client/demands/create/${serviceId}`,
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
			this.demands = [];
			this.meta = {};
			this.demand = {};
			this.loading = false;
			this.formLoading = false;
			this.errors = {};
			this.formData = {};
		},
		clear() {
			this.demand = {};
			this.serviceId = null;
			this.owner_info = null;
			this.vehicle_info = null;
		},
		/**
		 * Récupère les informations sur le propriétaire en fonction de l'identifiant fourni.
		 * Si le propriétaire est une entreprise, il récupère les informations sur l'entreprise.
		 * Sinon, il récupère les informations de l'individu.
		 *
		 * @param {string} npiOrIfu - L'identifiant du propriétaire. Il peut s'agir d'un identifiant national de fournisseur (NPI) ou d'une unité fiscale individuelle (IFU).
		 * @param {boolean} [isCompany=false] - Un drapeau indiquant si le propriétaire est une entreprise ou un particulier.
		 * @returns {Promise} Une promesse qui se résout avec les informations du propriétaire si la demande aboutit, ou qui est rejetée avec un message d'erreur si la demande échoue.
		 */
		fetchOwnerInfo(npiOrIfu, isCompany = false) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: isCompany ? `get-company/${npiOrIfu}` : `get-identity/${npiOrIfu}`,
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
		/**
		 * Récupérer les infos d'un véhicule
		 * @param {string} vin
		 * @param {string|null} ref
		 * @param is_car
		 * @return {Promise<unknown>}
		 */
		fetchVehicleInfo(vin, ref = null, is_car = 0) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `get-vehicle?vin=${vin}&customs_ref=${ref}&is_car=${is_car}`,
				})
					.then((response) => {
						this.vehicle_info = response.data;
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
		fetchVehicleSituation(vin) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `/client/demands/verify-vehicle-situation/${vin}`,
				})
					.then((response) => {
						this.vehicle_info = response.data.vehicle;
						this.reason = response.data.reimmatriculation_reason;
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
		fetchVehicles(npiOrIfu) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/client/get-vehicles?key=" + npiOrIfu,
				})
					.then((response) => {
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
		getCertificatInfo(reference) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `client/sale-declarations/${reference}`,
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
	},
});
