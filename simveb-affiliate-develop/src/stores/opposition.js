import {defineStore} from "pinia";
import client from "@/assets/js/axios/client.js";


export const useOppositionStore = defineStore("opposition", {
    state: () => ({
        loading: false,
        errors: {},
        custom_ref: null,
        vehicles: [],
        selectedVehicle: null,
        reason_for_opposition: null,
        opposition_file: null,
        isCompany: false,
        create: null
    }),

    actions: {
        loadCreate(){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "GET",
                    url: `/oppositions/create`,
                })
                    .then((response) => {
                        this.create = response.data;
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

        findVehicles() {
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "GET",
                    url: `/owner/vehicles?npi_or_ifu=${this.custom_ref}`,
                })
                    .then((response) => {
                        this.vehicles = response.data;
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

        storeOpposition(authorization_id){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "POST",
                    url: `/oppositions`,
                    data : {
                        npi_or_ifu: this.custom_ref,
                        authorization_id: authorization_id,
                        vehicles: [this.selectedVehicle],
                        opposition_file: this.opposition_file,
                        reason_for_opposition: this.reason_for_opposition
                    },
                    headers: {
                        ...client.defaults.headers,
                        "Content-Type": "multipart/form-data",
                    },
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

        updateOpposition(id){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "POST",
                    url: `/oppositions/${id}`,
                    data : {
                        opposition_file: this.opposition_file,
                        reason_for_opposition: this.reason_for_opposition,
                        _method: 'PUT'
                    },
                    headers: {
                        ...client.defaults.headers,
                        "Content-Type": "multipart/form-data",
                    },
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
        }
    }
});