import { defineStore } from "pinia";
import client from "/@src/composable/axiosClient";

export const useDemandStore = defineStore("demand", {
	state: (): {
		verificationModal: boolean;
		formLoading: boolean;
		validationModal: boolean;
		serviceAssignationModal: boolean;
		printOrderValidationModal: boolean;
		nextAction: null;
		managementCenterAssignationModal: boolean;
		staffAssignationModal: boolean;
		loading: boolean;
		demand: null;
		operate: boolean;
		meta: undefined;
		formData: undefined;
		demands: any[];
		errors: {};
	} => ({
		demands: [],
		meta: undefined,
		demand: null,
		errors: {},
		formData: undefined,
		loading: false,
		formLoading: false,
		nextAction: null,
		operate: false,
		managementCenterAssignationModal: false,
		serviceAssignationModal: false,
		staffAssignationModal: false,
		verificationModal: false,
		validationModal: false,
		printOrderValidationModal: false,
	}),
	getters: {},
	actions: {
		fetchDemands(options: string | string[][] | Record<string, string> | URLSearchParams | undefined) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "admin-demands?" + new URLSearchParams(options).toString(),
				})
					.then((response) => response.data)
					.then((response) => {
						if (response.data) {
							this.demands = response.data;
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

		getDemand(id: any) {
			return this.demands.find((demand) => demand.id === id) || this.fetchDemand(id);
		},

		fetchDemand(id: any) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: `demands/${id}`,
				})
					.then((response) => {
						this.demand = { ...response.data, id };
						this.nextAction = response.data.next_action;
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

		assignDemand(to: string, data: { demand_id: any; npi: string }) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `assign-demand-to-${to}`,
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

		emitPrintOrder(data: { demand_id: any; print_observations: any }) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "emit-print-order",
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

		closeDemand(data: { demand_id: any }) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "close-demand",
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

		printGrayCard(data: { demand_id: any }) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "print-orders/print-gray-card",
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

		validateDemand(demandID: string, approved: boolean, rejectReason?: string) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: approved ? "validate-demand" : "reject-demand",
					data: {
						demand_id: demandID,
						reason: rejectReason,
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

		validateDemandInterpol(treatmentID: string, approved: boolean, rejectReason?: string) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: approved ? "validate-demand-interpol" : "reject-demand-interpol",
					data: {
						treatment_id: treatmentID,
						reason: rejectReason,
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

		verifyDemand(treatmentID: string, verified: boolean, rejectReason?: string) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: verified ? "verify-demand" : "suspend-demand",
					data: {
						treatment_id: treatmentID,
						reason: rejectReason,
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

		validateControl(demandID: string, approved: boolean, observations: string) {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: "anatt-control-immatriculation-demand",
					data: {
						action: approved ? "validate" : "reject",
						immatriculation_demand_id: demandID,
						observations,
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

		loadTreatmentCreateData() {
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "treatments/create",
				})
					.then((response) => {
						this.formData = response.data;
						resolve(response.data);
					})
					.catch((error) => {
						this.errors = error.response.data.errors;
						reject(error.response.data.message);
					});
			});
		},

		generateImmatriculation() {
			this.errors = {};
			this.formLoading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "generate-immatriculation-number",
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
			this.demands = [];
			this.meta = {};
			this.demand = {};
			this.loading = false;
			this.formLoading = false;
			this.errors = {};
			this.formData = {};
		},
	},
});
